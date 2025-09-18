<?php

namespace App\Filament\Staff\Resources;

use App\Filament\Staff\Resources\FacturaResource\Pages;
use App\Filament\Staff\Resources\FacturaResource\RelationManagers;
use App\Models\Factura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('radicado_id')
                    ->relationship('radicado', 'id')
                    ->required(),
                Forms\Components\TextInput::make('numero')
                    ->required()
                    ->maxLength(40),
                Forms\Components\TextInput::make('estado')
                    ->required(),
                Forms\Components\TextInput::make('cliente_nombre')
                    ->maxLength(180),
                Forms\Components\TextInput::make('cliente_doc_tipo')
                    ->maxLength(10),
                Forms\Components\TextInput::make('cliente_doc_num')
                    ->maxLength(40),
                Forms\Components\TextInput::make('cliente_email')
                    ->email()
                    ->maxLength(190),
                Forms\Components\TextInput::make('cliente_telefono')
                    ->tel()
                    ->maxLength(40),
                Forms\Components\TextInput::make('cliente_direccion')
                    ->maxLength(200),
                Forms\Components\TextInput::make('cliente_ciudad')
                    ->maxLength(100),
                Forms\Components\TextInput::make('cliente_empresa')
                    ->maxLength(180),
                Forms\Components\TextInput::make('cliente_nit')
                    ->maxLength(40),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('iva')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('pagado')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('saldo')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('pdf_path')
                    ->maxLength(2048),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('radicado.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado'),
                Tables\Columns\TextColumn::make('cliente_nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_doc_tipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_doc_num')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_ciudad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_empresa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_nit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('iva')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pagado')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('saldo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pdf_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFacturas::route('/'),
            'create' => Pages\CreateFactura::route('/create'),
            'edit' => Pages\EditFactura::route('/{record}/edit'),
        ];
    }
}
