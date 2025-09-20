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
use Filament\Tables\Actions\Action as TablesAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;

    protected static ?string $navigationIcon  = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Operación';
    protected static ?string $navigationLabel = 'Facturas';
    protected static ?int    $navigationSort  = 40;

    public static function getEloquentQuery(): Builder
    {
        // Carga relaciones necesarias (radicado + entidad polimórfica, items y pagos)
        return parent::getEloquentQuery()
            ->with(['radicado.user', 'radicado.radicable', 'items', 'pagos']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Columna izquierda
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Datos de la factura')->schema([
                    Forms\Components\TextInput::make('numero')
                        ->label('Número')
                        ->disabled(),

                    Forms\Components\Select::make('estado')
                        ->label('Estado (BD)')
                        ->options([
                            'borrador' => 'Borrador',
                            'emitida'  => 'Emitida',
                            'anulada'  => 'Anulada',
                        ])
                        ->disabled(),

                    Forms\Components\Textarea::make('cliente_direccion')
                        ->label('Dirección (snapshot)')
                        ->disabled()
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Totales')->schema([
                    Forms\Components\TextInput::make('subtotal')->numeric()->prefix('Subtotal')->disabled(),
                    Forms\Components\TextInput::make('iva')->numeric()->prefix('IVA')->disabled(),
                    Forms\Components\TextInput::make('total')->numeric()->prefix('Total')->disabled(),
                    Forms\Components\TextInput::make('pagado')->numeric()->prefix('Pagado')->disabled(),
                    Forms\Components\TextInput::make('saldo')->numeric()->prefix('Saldo')->disabled(),
                ])->columns(3),
            ])->columnSpan(8),

            // Columna derecha
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Radicado / Cliente')->schema([
                    Forms\Components\Placeholder::make('pl_radicado')
                        ->label('Radicado')
                        ->content(fn (?Factura $record) => $record?->radicado?->numero ?? '—'),

                    Forms\Components\Placeholder::make('pl_cliente')
                        ->label('Cliente')
                        ->content(fn (?Factura $record) =>
                            $record?->radicado?->user?->name
                            ?? $record?->cliente_nombre
                            ?? '—'
                        ),

                    Forms\Components\Placeholder::make('pl_soporte')
                        ->label('Soporte')
                        ->content(function (?Factura $record) {
                            $rad = $record?->radicado;
                            if (! $rad) return '—';
                            $ent = $rad->radicable; // puede ser Soporte/Contrato/Pqr...
                            if ($ent instanceof \App\Models\Soporte) {
                                return $ent->asunto
                                    ?? $ent->titulo
                                    ?? ('Soporte #'.$ent->id);
                            }
                            return '—';
                        }),

                    Forms\Components\Placeholder::make('pl_creada')
                        ->label('Creada')
                        ->content(fn (?Factura $record) => optional($record?->created_at)->format('d/m/Y H:i') ?? '—'),
                ]),

                Forms\Components\Section::make('PDF')->schema([
                    Forms\Components\TextInput::make('pdf_path')
                        ->label('Ruta PDF (disco public)')
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

                Tables\Columns\TextColumn::make('radicado.user.name')
                    ->label('Cliente')
                    ->formatStateUsing(fn ($state, Factura $record) =>
                        $state ?? $record->cliente_nombre ?? '—'
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado (BD)')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'borrador' => 'gray',
                        'emitida'  => 'warning',
                        'anulada'  => 'danger',
                        default    => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado_pago')
                    ->label('Pago')
                    ->badge()
                    ->state(fn (Factura $r) => $r->saldo > 0 ? 'Abierta' : 'Pagada')
                    ->color(fn (Factura $r) => $r->saldo > 0 ? 'warning' : 'success'),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('COP', true),

                Tables\Columns\TextColumn::make('iva')
                    ->label('IVA')
                    ->money('COP', true)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('COP', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('saldo')
                    ->label('Saldo')
                    ->money('COP', true)
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado (BD)')
                    ->options([
                        'borrador' => 'Borrador',
                        'emitida'  => 'Emitida',
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

                // Registrar anticipo en factura_pagos
                Tables\Actions\Action::make('registrar_abono')
                    ->label('Registrar anticipo')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn (Factura $record) => $record->estado !== 'anulada')
                    ->form([
                        Forms\Components\TextInput::make('monto')
                            ->label('Monto')
                            ->required()
                            ->suffix('COP'),
                        Forms\Components\Select::make('metodo')
                            ->label('Método')
                            ->options([
                                'efectivo'      => 'Efectivo',
                                'transferencia' => 'Transferencia',
                                'tarjeta'       => 'Tarjeta',
                                'nequi'         => 'Nequi',
                                'daviplata'     => 'Daviplata',
                                'otro'          => 'Otro',
                            ])->default('efectivo')->required(),
                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha de pago')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('referencia')
                            ->label('Referencia')
                            ->maxLength(190),
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->rows(2),
                    ])
                    ->action(function (Factura $record, array $data, TablesAction $action) {
                        try {
                            // Normaliza montos tipo "1.234,56"
                            $raw  = (string) ($data['monto'] ?? '');
                            $norm = is_string($raw) ? str_replace(['.', ','], ['', '.'], $raw) : $raw;
                            if (!is_numeric($norm) || (float)$norm <= 0) {
                                throw ValidationException::withMessages(['monto' => 'Monto inválido.']);
                            }
                            $monto = round((float)$norm, 2);

                            // Recalcula antes para leer saldo correcto
                            $record->load(['items','pagos']);
                            $record->recalcularTotales();

                            if ($monto > (float)$record->saldo) {
                                throw ValidationException::withMessages([
                                    'monto' => "El monto ($monto) supera el saldo ({$record->saldo}).",
                                ]);
                            }

                            DB::transaction(function () use ($record, $data, $monto) {
                                $record->registrarAnticipo([
                                    'user_id'    => auth()->id(),
                                    'monto'      => $monto,
                                    'moneda'     => 'COP',
                                    'metodo'     => $data['metodo'] ?? 'efectivo',
                                    'referencia' => $data['referencia'] ?? null,
                                    'fecha_pago' => optional($data['fecha'])->format('Y-m-d') ?? now()->toDateString(),
                                    'notas'      => $data['notas'] ?? null,
                                ]);
                            });

                            // Refresca y notifica
                            $record->refresh();
                            Notification::make()
                                ->title('Anticipo registrado')
                                ->body('Se actualizó el pagado y el saldo.')
                                ->success()
                                ->send();

                            // refrescar tabla (Livewire) si aplica
                            $action->getLivewire()->dispatch('refresh');
                        } catch (ValidationException $ve) {
                            throw $ve; // Muestra errores en el modal
                        } catch (\Throwable $e) {
                            report($e);
                            Notification::make()
                                ->title('Error registrando anticipo')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            $action->halt();
                        }
                    }),

                Tables\Actions\Action::make('generar_pdf')
                    ->label('Generar / Actualizar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->requiresConfirmation()
                    ->action(function (Factura $record, TablesAction $action) {
                        $view = view()->exists('pdf.factura') ? 'pdf.factura' : null;

                        if (! $view) {
                            Notification::make()
                                ->title('Vista PDF no encontrada')
                                ->body('Crea resources/views/pdf/factura.blade.php')
                                ->danger()->send();
                            return;
                        }

                        $record->load(['radicado.user', 'radicado.radicable', 'items', 'pagos']);

                        $soporte = null;
                        if ($record->radicado && $record->radicado->radicable instanceof \App\Models\Soporte) {
                            $soporte = $record->radicado->radicable;
                        }

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, [
                            'factura'  => $record,
                            'radicado' => $record->radicado,
                            'soporte'  => $soporte,
                            'cliente'  => $record->radicado?->user,
                            'items'    => $record->items,
                            'pagos'    => $record->pagos,
                            'mostrar_pagos' => true,
                        ])->setPaper('letter');

                        $path = "facturas/factura-{$record->id}.pdf";
                        Storage::disk('public')->put($path, $pdf->output());
                        $record->update(['pdf_path' => $path]);

                        Notification::make()
                            ->title('PDF generado')
                            ->success()->send();

                        $action->getLivewire()->dispatch('refresh');
                    }),

                // >>> Imprimir POS (MODO PRUEBAS: HTML con window.print())
                    Tables\Actions\Action::make('imprimir_pos')
                        ->label('Imprimir POS')
                        ->icon('heroicon-o-printer')
                        ->visible(fn (Factura $record) => $record->estado !== 'anulada')
                        ->action(function (Factura $record) {
                            $url = route('facturas.pos.print', $record);
                            return redirect()->to($url); // ✅ esto sí redirige

                        // === OPCIÓN PDF POS (DESACTIVADA/COMENTADA) ===
                        // $path = "facturas/pos-{$record->id}.pdf";
                        // if (!Storage::disk('public')->exists($path)) {
                        //     $view = view()->exists('pdf.factura_pos') ? 'pdf.factura_pos' : null;
                        //     if (! $view) {
                        //         Notification::make()
                        //             ->title('Vista POS no encontrada')
                        //             ->body('Crea resources/views/pdf/factura_pos.blade.php')
                        //             ->danger()->send();
                        //         return;
                        //     }
                        //     $record->load(['items','pagos','radicado.user']);
                        //     $itemsCount   = $record->items->count();
                        //     $pagosCount   = $record->pagos->count();
                        //     $mostrarPagos = true;
                        //     $base   = 360;
                        //     $byItem = 28 * max(1, $itemsCount);
                        //     $byPay  = $mostrarPagos ? (22 * max(0, $pagosCount)) : 0;
                        //     $height = $base + $byItem + $byPay;
                        //     $width  = 226.77;
                        //     $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, [
                        //         'factura'        => $record,
                        //         'mostrar_pagos'  => $mostrarPagos,
                        //         'emisor'         => [
                        //             'razon_social' => config('app.name'),
                        //             'nit'          => config('app.company_nit', ''),
                        //             'dv'           => config('app.company_dv', ''),
                        //         ],
                        //     ])->setPaper([0, 0, $width, $height], 'portrait');
                        //     Storage::disk('public')->put($path, $pdf->output());
                        // }
                        // $urlPdf = Storage::disk('public')->url($path);
                        // Notification::make()->title('POS listo')->success()->send();
                        // $action->successRedirectUrl($urlPdf);
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
                    ->action(function (Factura $record, TablesAction $action) {
                        $record->estado = 'anulada';
                        $record->save();

                        Notification::make()
                            ->title('Factura anulada')
                            ->warning()->send();

                        $action->getLivewire()->dispatch('refresh');
                    }),

                Tables\Actions\Action::make('restaurar')
                    ->label('Restaurar')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->visible(fn (Factura $record) => $record->estado === 'anulada')
                    ->action(function (Factura $record, TablesAction $action) {
                        $record->estado = 'emitida'; // o 'borrador' según tu flujo
                        $record->save();

                        Notification::make()
                            ->title('Factura restaurada')
                            ->success()->send();

                        $action->getLivewire()->dispatch('refresh');
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            // Agrega aquí PagosRelationManager si lo creas:
            // \App\Filament\Resources\FacturaResource\RelationManagers\PagosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFacturas::route('/'),
            'view'  => Pages\ViewFactura::route('/{record}'),
            // 'print' => Pages\ImprimirFactura::route('/{record}/print'),
        ];
    }
}
