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
                Tables\Columns\TextColumn::make('concepto')->label('Concepto')->wrap(),
                Tables\Columns\TextColumn::make('unidad')->label('Unidad')->toggleable(),
                Tables\Columns\TextColumn::make('cantidad')->label('Cant.')->numeric(0),
                Tables\Columns\TextColumn::make('precio_unitario')->label('V. Unitario')->money(fn ($record) => $record->factura->moneda ?? 'COP', true),
                Tables\Columns\TextColumn::make('iva_pct')->label('IVA %')->numeric(2),
                Tables\Columns\TextColumn::make('subtotal')->label('Subtotal')->money(fn ($record) => $record->factura->moneda ?? 'COP', true),
                Tables\Columns\TextColumn::make('iva_monto')->label('IVA')->money(fn ($record) => $record->factura->moneda ?? 'COP', true),
                Tables\Columns\TextColumn::make('total')->label('Total')->money(fn ($record) => $record->factura->moneda ?? 'COP', true),
            ])
            ->paginated(false)
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
