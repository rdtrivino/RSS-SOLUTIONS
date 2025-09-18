<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PqrResource\Pages;
use App\Models\Pqr;
use App\Models\Radicado;
use App\Models\RadicadoRespuesta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\URL; // <- Descomenta si usas ruta firmada en descargar_pdf
use Filament\Notifications\Notification;

class PqrResource extends Resource
{
    protected static ?string $model = Pqr::class;

    protected static ?string $navigationGroup = 'Operación';
    protected static ?string $navigationIcon  = 'heroicon-o-inbox';
    protected static ?string $navigationLabel = 'PQR';
    protected static ?int    $navigationSort  = 22;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['radicado.user']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos de la PQR')->schema([
                Forms\Components\Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'peticion'     => 'Petición',
                        'queja'        => 'Queja',
                        'reclamo'      => 'Reclamo',
                        'sugerencia'   => 'Sugerencia',
                        'felicitacion' => 'Felicitación',
                    ])->required()->native(false),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')->rows(6)->required(),

                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'radicado'   => 'Radicado',
                        'en_proceso' => 'En proceso',
                        'resuelto'   => 'Resuelto',
                    ])->required()->default('radicado')->native(false),

                Forms\Components\Select::make('user_id')
                    ->label('Usuario (opcional)')
                    ->relationship('user', 'name')
                    ->searchable()->preload()->native(false)
                    ->placeholder('Anónimo'),
            ])->columns(2),

            Forms\Components\Section::make('Radicado')->schema([
                Forms\Components\TextInput::make('radicado_numero')
                    ->label('Número')->disabled()->dehydrated(false)
                    ->helperText('Se genera automáticamente al crear el registro.'),
                Forms\Components\TextInput::make('radicado_modulo')
                    ->label('Módulo')->disabled()->dehydrated(false)->default('pqr'),
            ])->columns(2),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
->columns([
    Tables\Columns\TextColumn::make('radicado.numero')
        ->label('Radicado')
        ->searchable()
        ->sortable()
        ->copyable(),

    Tables\Columns\TextColumn::make('user.name')
        ->label('Usuario')
        ->toggleable()
        ->searchable(),

    Tables\Columns\BadgeColumn::make('tipo')
        ->label('Tipo')
        ->formatStateUsing(fn (?string $state) => $state ? ucfirst($state) : '-')
        ->color(fn (?string $state) => match ($state) {
            'peticion'     => 'info',
            'queja'        => 'warning',
            'reclamo'      => 'danger',
            'sugerencia'   => 'gray',
            'felicitacion' => 'success',
            default        => 'secondary',
        })
        ->sortable(),

    Tables\Columns\BadgeColumn::make('estado')
        ->label('Estado')
        ->formatStateUsing(fn (?string $state) => $state ? ucfirst(str_replace('_', ' ', $state)) : '-')
        ->color(fn (?string $state) => match ($state) {
            'radicado'   => 'gray',
            'en_proceso' => 'info',
            'resuelto'   => 'success',
            default      => 'secondary',
        })
        ->sortable(),

    Tables\Columns\TextColumn::make('created_at')
        ->label('Creado')
        ->dateTime('d/m/Y H:i')
        ->sortable(),
])

            ->filters([
                Tables\Filters\SelectFilter::make('tipo')->options([
                    'peticion'     => 'Petición',
                    'queja'        => 'Queja',
                    'reclamo'      => 'Reclamo',
                    'sugerencia'   => 'Sugerencia',
                    'felicitacion' => 'Felicitación',
                ]),
                Tables\Filters\SelectFilter::make('estado')->options([
                    'radicado'   => 'Radicado',
                    'en_proceso' => 'En proceso',
                    'resuelto'   => 'Resuelto',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Action::make('cambiar_estado')
                    ->label('Cambiar estado')
                    ->icon('heroicon-m-arrow-path')
                    ->form([
                        Forms\Components\Select::make('nuevo_estado')
                            ->label('Nuevo estado')
                            ->options(fn (?Pqr $record) => match ($record?->estado) {
                                null, '', 'radicado' => [
                                    'radicado'   => 'Radicado',
                                    'en_proceso' => 'En proceso',
                                    'resuelto'   => 'Resuelto',
                                ],
                                'en_proceso' => [
                                    'en_proceso' => 'En proceso',
                                    'resuelto'   => 'Resuelto',
                                ],
                                default => [
                                    'resuelto' => 'Resuelto',
                                ],
                            })
                            ->required(),
                        Forms\Components\Textarea::make('nota')->label('Nota interna')->rows(2),
                    ])
                    ->action(function (Pqr $record, array $data) {
                        $nuevo = $data['nuevo_estado'];

                        // Validación de transición simple
                        $validas = match ($record->estado) {
                            null, '', 'radicado' => ['radicado', 'en_proceso', 'resuelto'],
                            'en_proceso'         => ['en_proceso', 'resuelto'],
                            default              => ['resuelto'],
                        };
                        if (! in_array($nuevo, $validas, true)) {
                            Notification::make()->title('Transición inválida')
                                ->body('No es posible cambiar al estado seleccionado desde el estado actual.')
                                ->warning()->send();
                            return;
                        }

                        // Actualiza estado
                        $record->update(['estado' => $nuevo]);

                        // Asegura radicado
                        $radicado = self::ensureRadicado($record);

                        // Registra respuesta
                        RadicadoRespuesta::create([
                            'radicado_id' => $radicado->id,
                            'user_id'     => Auth::id(),
                            'formato'     => 'pqr',
                            'resultado'   => $nuevo,
                            'data'        => ['nota' => $data['nota'] ?? null],
                            'cierra_caso' => $nuevo === 'resuelto',
                        ]);

                        Notification::make()->title('Estado actualizado')->success()->send();
                    }),

                Action::make('adjuntar_pdf')
                    ->label('Adjuntar/Actualizar PDF')
                    ->icon('heroicon-o-paper-clip')
                    ->form([
                        Forms\Components\FileUpload::make('pdf')
                            ->label('Archivo PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('radicados/pqr')
                            ->disk('public')
                            ->required(),
                        Forms\Components\Textarea::make('nota')->label('Nota')->rows(2),
                    ])
                    ->action(function (Pqr $record, array $data) {
                        $radicado = self::ensureRadicado($record);

                        RadicadoRespuesta::create([
                            'radicado_id' => $radicado->id,
                            'user_id'     => Auth::id(),
                            'formato'     => 'pqr',
                            'resultado'   => $record->estado,
                            'data'        => ['nota' => $data['nota'] ?? null],
                            'cierra_caso' => $record->estado === 'resuelto',
                            'pdf_path'    => $data['pdf'],
                        ]);

                        Notification::make()->title('PDF adjuntado')->success()->send();
                    }),

                Action::make('cerrar')
                    ->label('Cerrar como Resuelto')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Pqr $record) => $record->estado !== 'resuelto')
                    ->form([
                        Forms\Components\FileUpload::make('pdf')
                            ->label('Adjuntar PDF de cierre (opcional)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('radicados/pqr')
                            ->disk('public'),
                        Forms\Components\Textarea::make('nota')->label('Nota de cierre')->rows(2),
                    ])
                    ->action(function (Pqr $record, array $data) {
                        $record->update(['estado' => 'resuelto']);
                        $radicado = self::ensureRadicado($record);

                        $resp = RadicadoRespuesta::create([
                            'radicado_id' => $radicado->id,
                            'user_id'     => Auth::id(),
                            'formato'     => 'pqr',
                            'resultado'   => 'resuelto',
                            'data'        => ['nota' => $data['nota'] ?? null],
                            'cierra_caso' => true,
                            'pdf_path'    => $data['pdf'] ?? null,
                        ]);

                        // Si NO adjuntaron PDF, intenta generar con la vista pdf.pqr_acuse (si existe)
                        if (empty($data['pdf']) && view()->exists('pdf.pqr_acuse')) {
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pqr_acuse', [
                                'radicado'  => $radicado->load('user'),
                                'pqr'       => $record,
                                'respuesta' => $resp,
                            ]);
                            $path = "radicados/{$radicado->id}/pqr-{$resp->id}.pdf";
                            Storage::disk('public')->put($path, $pdf->output());
                            $resp->update(['pdf_path' => $path]);
                        }

                        Notification::make()->title('PQR cerrada')->success()->send();
                    }),

                Action::make('descargar_pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url(function (Pqr $record) {
                        if (! $record->radicado) return null;

                        $ultima = RadicadoRespuesta::query()
                            ->where('radicado_id', $record->radicado->id)
                            ->where('formato', 'pqr')
                            ->whereNotNull('pdf_path')
                            ->latest('id')
                            ->first();

                        if (! $ultima?->pdf_path) return null;

                        // Opción A: URL pública del disco
                        return Storage::disk('public')->url($ultima->pdf_path);

                        // Opción B: Ruta firmada a tu controlador (descomenta para usarla)
                        // return URL::signedRoute('radicado.pdf', ['radicado' => $record->radicado->id]);
                    })
                    ->visible(function (Pqr $record) {
                        if (! $record->radicado) return false;

                        return RadicadoRespuesta::query()
                            ->where('radicado_id', $record->radicado->id)
                            ->where('formato', 'pqr')
                            ->whereNotNull('pdf_path')
                            ->exists();
                    })
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPqrs::route('/'),
            'create' => Pages\CreatePqr::route('/create'),
            'edit'   => Pages\EditPqr::route('/{record}/edit'),
            'view'   => Pages\ViewPqr::route('/{record}'),
        ];
    }

    /* ================== Helpers ================== */

    /** Asegura que exista un Radicado para la PQR (y lo crea si falta). */
    protected static function ensureRadicado(Pqr $record): Radicado
    {
        $radicado = $record->radicado;
        if ($radicado) return $radicado;

        return $record->radicado()->create([
            'numero'  => 'PQR-'.date('Y').'-'.str_pad((string) $record->id, 6, '0', STR_PAD_LEFT),
            'modulo'  => 'pqr',
            'user_id' => $record->user_id ?? Auth::id(),
        ]);
    }
}
