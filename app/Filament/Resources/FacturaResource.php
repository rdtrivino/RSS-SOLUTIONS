<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacturaResource\Pages;
use App\Filament\Resources\FacturaResource\RelationManagers\ItemsRelationManager;
use App\Models\Factura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;

    protected static ?string $navigationIcon  = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Operación';
    protected static ?string $navigationLabel = 'Facturas';
    protected static ?int    $navigationSort  = 40;

    public static function getEloquentQuery(): Builder
    {
        // Eager para radicado, usuario e items
        return parent::getEloquentQuery()->with(['radicado.user', 'items', 'pagos']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Columna izquierda (principal)
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Datos de la factura')->schema([
                    Forms\Components\TextInput::make('numero')
                        ->label('Número')
                        ->disabled(),

                    Forms\Components\Select::make('estado')
                        ->label('Estado')
                        ->options([
                            'borrador' => 'Borrador',
                            'abierta'  => 'Abierta',
                            'pagada'   => 'Pagada',
                            'anulada'  => 'Anulada',
                        ])
                        ->disabled(),

                    Forms\Components\TextInput::make('moneda')
                        ->label('Moneda')
                        ->disabled(),

                    // Usamos 'notas' (coincide con la migración sugerida)
                    Forms\Components\Textarea::make('notas')
                        ->label('Notas / Observaciones')
                        ->rows(3)
                        ->helperText('Notas u observaciones visibles.')
                        ->disabled()
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Totales')->schema([
                    // OJO: nombres alineados con la tabla: subtotal, iva, total, anticipo, saldo
                    Forms\Components\TextInput::make('subtotal')->numeric()->prefix('Subtotal')->disabled(),
                    Forms\Components\TextInput::make('iva')->numeric()->prefix('IVA')->disabled(),
                    Forms\Components\TextInput::make('total')->numeric()->prefix('Total')->disabled(),
                    Forms\Components\TextInput::make('anticipo')->numeric()->prefix('Anticipado')->disabled(),
                    Forms\Components\TextInput::make('saldo')->numeric()->prefix('Saldo')->disabled(),
                ])->columns(3),
            ])->columnSpan(8),

            // Columna derecha (info relacionada)
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Radicado / Cliente')->schema([
                    // Mejor usar Placeholder para mostrar relaciones
                    Forms\Components\Placeholder::make('pl_radicado')
                        ->label('Radicado')
                        ->content(fn (?Factura $record) => $record?->radicado?->numero ?? '—'),

                    Forms\Components\Placeholder::make('pl_cliente')
                        ->label('Cliente')
                        ->content(fn (?Factura $record) => $record?->user?->name ?? $record?->cliente_nombre ?? '—'),

                    Forms\Components\Placeholder::make('pl_creada')
                        ->label('Creada')
                        ->content(fn (?Factura $record) => optional($record?->created_at)->format('d/m/Y H:i') ?? '—'),
                ]),

                Forms\Components\Section::make('PDF')->schema([
                    Forms\Components\TextInput::make('pdf_path')
                        ->label('Ruta PDF')
                        ->helperText('Se guarda en el disco "public".')
                        ->disabled(),
                ]),
            ])->columnSpan(4),
        ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->label('Factura')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('radicado.numero')
                    ->label('Radicado')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->formatStateUsing(fn ($state, Factura $record) =>
                        $state ?? $record->cliente_nombre ?? '—'
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'borrador' => 'gray',
                        'abierta'  => 'warning',
                        'pagada'   => 'success',
                        'anulada'  => 'danger',
                        default    => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money(fn (Factura $record) => $record->moneda ?? 'COP', true),

                Tables\Columns\TextColumn::make('iva')
                    ->label('IVA')
                    ->money(fn (Factura $record) => $record->moneda ?? 'COP', true)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money(fn (Factura $record) => $record->moneda ?? 'COP', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('saldo')
                    ->label('Saldo')
                    ->money(fn (Factura $record) => $record->moneda ?? 'COP', true)
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'abierta'  => 'Abierta',
                        'pagada'   => 'Pagada',
                        'anulada'  => 'Anulada',
                    ]),
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['hasta'] ?? null, fn ($q, $h) => $q->whereDate('created_at', '<=', $h));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                // Registrar anticipo usando el servicio (quedará en factura_pagos)
                Tables\Actions\Action::make('registrar_abono')
                    ->label('Registrar anticipo')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn (Factura $record) => ! in_array($record->estado, ['pagada', 'anulada'], true))
                    ->form([
                        Forms\Components\TextInput::make('monto')
                            ->numeric()
                            ->minValue(0.01)
                            ->label('Monto')
                            ->required(),
                        Forms\Components\Select::make('metodo')
                            ->label('Método')
                            ->options([
                                'efectivo'     => 'Efectivo',
                                'transferencia'=> 'Transferencia',
                                'tarjeta'      => 'Tarjeta',
                                'nequi'        => 'Nequi',
                                'daviplata'    => 'Daviplata',
                                'otro'         => 'Otro',
                            ])->default('efectivo')->required(),
                        Forms\Components\DatePicker::make('fecha_pago')
                            ->label('Fecha de pago')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('referencia')
                            ->label('Referencia')
                            ->maxLength(190),
                        Forms\Components\Textarea::make('notas')->rows(2)->label('Notas'),
                    ])
                    ->action(function (Factura $record, array $data) {
                        try {
                            app(\App\Services\FacturacionService::class)->registrarAnticipo(
                                $record,
                                (float) $data['monto'],
                                [
                                    'user_id'    => auth()->id(),
                                    'moneda'     => $record->moneda ?? 'COP',
                                    'metodo'     => $data['metodo'] ?? 'efectivo',
                                    'referencia' => $data['referencia'] ?? null,
                                    'fecha_pago' => $data['fecha_pago'] ?? now()->toDateString(),
                                    'notas'      => $data['notas'] ?? null,
                                ]
                            );

                            // Actualiza estado según saldo
                            $record->refresh();
                            if ($record->estado !== 'anulada') {
                                $record->estado = $record->saldo > 0 ? 'abierta' : 'pagada';
                                $record->save();
                            }

                            Notification::make()->title('Anticipo registrado')->success()->send();
                        } catch (\Throwable $e) {
                            report($e);
                            Notification::make()->title('Error registrando anticipo')->danger()->send();
                        }
                    }),

                Tables\Actions\Action::make('generar_pdf')
                    ->label('Generar / Actualizar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->requiresConfirmation()
                    ->action(function (Factura $record) {
                        $view = view()->exists('pdf.factura') ? 'pdf.factura' : null;

                        if (! $view) {
                            Notification::make()
                                ->title('Vista PDF no encontrada')
                                ->body('Crea resources/views/pdf/factura.blade.php')
                                ->danger()->send();
                            return;
                        }

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, [
                            'factura'  => $record->load(['radicado.user', 'items', 'pagos']),
                            'radicado' => $record->radicado,
                            'cliente'  => $record->user,
                            'items'    => $record->items,
                            'pagos'    => $record->pagos,
                        ])->setPaper('letter');

                        $path = "facturas/factura-{$record->id}.pdf";
                        Storage::disk('public')->put($path, $pdf->output());
                        $record->update(['pdf_path' => $path]);

                        Notification::make()
                            ->title('PDF generado')
                            ->success()->send();
                    }),

                Tables\Actions\Action::make('descargar_pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url(fn (Factura $record) => $record->pdf_path ? Storage::disk('public')->url($record->pdf_path) : null)
                    ->visible(fn (Factura $record) => ! empty($record->pdf_path))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Factura $record) => $record->estado !== 'anulada')
                    ->action(function (Factura $record) {
                        $record->estado = 'anulada';
                        $record->save();

                        Notification::make()
                            ->title('Factura anulada')
                            ->warning()->send();
                    }),

                Tables\Actions\Action::make('restaurar')
                    ->label('Restaurar')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->visible(fn (Factura $record) => $record->estado === 'anulada')
                    ->action(function (Factura $record) {
                        $record->estado = $record->saldo > 0 ? 'abierta' : 'pagada';
                        $record->save();

                        Notification::make()
                            ->title('Factura restaurada')
                            ->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            // Asegúrate de tener este RelationManager creado
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFacturas::route('/'),
            'view'  => Pages\ViewFactura::route('/{record}'),
        ];
    }
}
