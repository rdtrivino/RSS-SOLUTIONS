<?php

namespace App\Filament\Resources\FacturaResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title       = 'Ítems de la factura';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('concepto')
                    ->label('Concepto')
                    ->wrap(),

                Tables\Columns\TextColumn::make('unidad')
                    ->label('Unidad')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('cantidad')
                    ->label('Cant.')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 2)),

                Tables\Columns\TextColumn::make('precio_unitario')
                    ->label('V. Unitario')
                    ->money('COP', true),

                Tables\Columns\TextColumn::make('iva_pct')
                    ->label('IVA %')
                    ->formatStateUsing(fn ($state) => number_format((float)$state, 2)),

                // Subtotal calculado = cantidad * precio_unitario
                Tables\Columns\TextColumn::make('computed_subtotal')
                    ->label('Subtotal')
                    ->state(fn ($record) => (float)$record->cantidad * (float)$record->precio_unitario)
                    ->money('COP', true),

                // IVA calculado = subtotal * (iva_pct / 100)
                Tables\Columns\TextColumn::make('computed_iva')
                    ->label('IVA')
                    ->state(function ($record) {
                        $sub = (float)$record->cantidad * (float)$record->precio_unitario;
                        return $sub * ((float)$record->iva_pct / 100);
                    })
                    ->money('COP', true),

                // Total viene de la columna 'total' de factura_items (se recalcula en el modelo)
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('COP', true),
            ])
            ->paginated(false)
            ->headerActions([]) // dejas solo lectura; si quieres crear/editar, te paso el form
            ->actions([])       // idem
            ->bulkActions([]);
    }
}
