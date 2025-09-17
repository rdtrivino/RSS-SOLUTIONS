<?php

namespace App\Filament\Resources;

use App\Models\Contrato;
use App\Models\RadicadoRespuesta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class ContratoResource extends Resource
{
    protected static ?string $model = Contrato::class;

    protected static ?string $navigationGroup = 'Operación';
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Contratos';
    protected static ?int    $navigationSort  = 21;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['radicado.user']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Cliente')->schema([
                Forms\Components\TextInput::make('nombre')->label('Nombre')->disabled(),
                Forms\Components\TextInput::make('email')->label('Correo')->disabled(),
                Forms\Components\TextInput::make('celular')->label('Celular')->disabled(),
                Forms\Components\TextInput::make('empresa')->label('Empresa')->disabled(),
                Forms\Components\TextInput::make('nit')->label('NIT')->disabled(),
            ])->columns(2),

            Forms\Components\Section::make('Servicio')->schema([
                Forms\Components\TextInput::make('servicio')->disabled(),
                Forms\Components\TextInput::make('especificar')->disabled(),
                Forms\Components\Textarea::make('mensaje')->rows(4)->disabled(),
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'en_estudio' => 'En estudio',
                        'aceptado'   => 'Aceptado',
                        'rechazado'  => 'Rechazado',
                    ])->disabled(),
            ])->columns(2),

            // (Opcional) Permite fijar/editar la plantilla en el registro.
            Forms\Components\Select::make('plantilla')
                ->label('Plantilla PDF (override)')
                ->options(self::templateOptions())
                ->helperText('Si la dejas vacía, se detecta automáticamente por el campo "Servicio".')
                ->native(false)
                ->searchable()
                ->nullable(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('radicado.numero')
                    ->label('Radicado')->searchable()->sortable()->copyable(),
                Tables\Columns\TextColumn::make('nombre')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('empresa')->label('Empresa')->toggleable(),
                Tables\Columns\TextColumn::make('servicio')->badge()->label('Servicio')->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()->label('Estado')
                    ->color(fn (string $state) => match ($state) {
                        'en_estudio' => 'info',
                        'aceptado'   => 'success',
                        'rechazado'  => 'danger',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('plantilla')
                    ->label('Plantilla')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')->options([
                    'en_estudio' => 'En estudio',
                    'aceptado'   => 'Aceptado',
                    'rechazado'  => 'Rechazado',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Action::make('cambiar_estado')
                    ->label('Cambiar estado')
                    ->icon('heroicon-m-arrow-path')
                    ->visible(fn (Contrato $record) => ! in_array($record->estado, ['aceptado', 'rechazado'], true))
                    ->form([
                        Forms\Components\Select::make('nuevo_estado')
                            ->label('Nuevo estado')
                            ->options(fn (?Contrato $record) => match ($record?->estado) {
                                null, '', 'en_estudio' => [
                                    'en_estudio' => 'En estudio',
                                    'aceptado'   => 'Aceptado',
                                    'rechazado'  => 'Rechazado',
                                ],
                                default => [
                                    'aceptado'   => 'Aceptado',
                                    'rechazado'  => 'Rechazado',
                                ],
                            })
                            ->required()
                            ->live(),

                        // ← ESTE ES EL SELECTOR DE PLANTILLA EN EL MODAL
                        Forms\Components\Select::make('plantilla_override')
                            ->label('Plantilla PDF (solo si Aceptado)')
                            ->options(self::templateOptions())
                            ->helperText('Si no eliges, se usará la del contrato (si existe) o se detecta por "Servicio".')
                            ->native(false)
                            ->searchable()
                            ->visible(fn (Get $get) => $get('nuevo_estado') === 'aceptado')
                            ->nullable(),
                    ])
                    ->action(function (Contrato $record, array $data) {
                        if (in_array($record->estado, ['aceptado', 'rechazado'], true)) {
                            Notification::make()
                                ->title('Contrato finalizado')
                                ->body('Este contrato ya tiene una decisión final y no puede cambiarse.')
                                ->danger()->send();
                            return;
                        }

                        $nuevo      = $data['nuevo_estado'];
                        $plantillaO = $data['plantilla_override'] ?? null;

                        $validas = $record->estado === 'en_estudio' || ! $record->estado
                            ? ['en_estudio', 'aceptado', 'rechazado']
                            : ['aceptado', 'rechazado'];

                        if (! in_array($nuevo, $validas, true)) {
                            Notification::make()
                                ->title('Transición inválida')
                                ->body('No es posible cambiar al estado seleccionado desde el estado actual.')
                                ->warning()->send();
                            return;
                        }

                        // 1) Actualizar estado (y guardar plantilla si eligen override)
                        $updates = ['estado' => $nuevo];
                        if ($plantillaO) {
                            $updates['plantilla'] = $plantillaO;
                        }
                        $record->update($updates);

                        // 2) Registrar respuesta
                        $resp = RadicadoRespuesta::create([
                            'radicado_id' => $record->radicado?->id,
                            'user_id'     => Auth::id(),
                            'formato'     => 'contrato',
                            'resultado'   => $nuevo,
                            'data'        => ['estado' => $nuevo],
                            'cierra_caso' => in_array($nuevo, ['aceptado', 'rechazado'], true),
                        ]);

                        // 3) Si quedó final, generar PDF con la plantilla elegida/guardada/auto
                        if (in_array($nuevo, ['aceptado', 'rechazado'], true) && $record->radicado) {
                            $view = $nuevo === 'aceptado'
                                ? self::resolveTemplateView($record, $plantillaO)
                                : 'pdf.contrato-rechazo';

                            $viewToUse = view()->exists($view) ? $view : 'pdf.contrato-propuesta';

                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewToUse, [
                                'radicado'  => $record->radicado->load('user'),
                                'contrato'  => $record,
                                'respuesta' => $resp,
                            ]);

                            $path = "radicados/{$record->radicado->id}/contrato-{$resp->id}.pdf";
                            Storage::disk('public')->put($path, $pdf->output());
                            $resp->update(['pdf_path' => $path]);
                        }

                        Notification::make()->title('Estado actualizado')->success()->send();
                    }),

                Action::make('descargar_pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url(function (Contrato $record) {
                        if (! $record->radicado) return null;

                        $ultima = RadicadoRespuesta::query()
                            ->where('radicado_id', $record->radicado->id)
                            ->where('formato', 'contrato')
                            ->whereNotNull('pdf_path')
                            ->latest('id')
                            ->first();

                        return $ultima?->pdf_path
                            ? Storage::disk('public')->url($ultima->pdf_path)
                            : null;
                    })
                    ->visible(function (Contrato $record) {
                        if (! $record->radicado) return false;

                        return RadicadoRespuesta::query()
                            ->where('radicado_id', $record->radicado->id)
                            ->where('formato', 'contrato')
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
            'index' => \App\Filament\Resources\ContratoResource\Pages\ListContratos::route('/'),
            'view'  => \App\Filament\Resources\ContratoResource\Pages\ViewContrato::route('/{record}'),
        ];
    }

    /** Opciones de plantillas disponibles (clave = view path). */
    private static function templateOptions(): array
    {
        return [
            'pdf.propuestas.desarrollo'       => 'Desarrollo',
            'pdf.propuestas.redes'            => 'Redes',
            'pdf.propuestas.soporte'          => 'Soporte / Mantenimiento',
            'pdf.propuestas.integraciones'    => 'Integraciones (APIs)',
            'pdf.propuestas.automatizaciones' => 'Automatizaciones / RPA',
            'pdf.propuestas.consultoria'      => 'Consultoría',
            'pdf.contrato-propuesta'          => 'Genérica',
        ];
    }

    /** Selecciona la vista PDF (override > columna plantilla > detección por servicio). */
    private static function resolveTemplateView(Contrato $record, ?string $override = null): string
    {
        if ($override && view()->exists($override)) {
            return $override;
        }

        if (!empty($record->plantilla) && view()->exists($record->plantilla)) {
            return $record->plantilla;
        }

        $s = Str::of((string) $record->servicio)->ascii()->lower();

        return match (true) {
            $s->contains('desarrollo') || $s->contains('web') || $s->contains('app') || $s->contains('sitio')
                => 'pdf.propuestas.desarrollo',

            $s->contains('red') || $s->contains('network') || $s->contains('wifi')
                => 'pdf.propuestas.redes',

            $s->contains('soporte') || $s->contains('mantenimiento')
                => 'pdf.propuestas.soporte',

            $s->contains('integracion') || $s->contains('api')
                => 'pdf.propuestas.integraciones',

            $s->contains('automatiz') || $s->contains('rpa')
                => 'pdf.propuestas.automatizaciones',

            $s->contains('consult')
                => 'pdf.propuestas.consultoria',

            default
                => 'pdf.contrato-propuesta',
        };
    }
}
