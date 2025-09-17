<?php

namespace App\Filament\Resources;

use App\Models\Radicado;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class RadicadoResource extends Resource
{
    protected static ?string $model = Radicado::class;
    protected static ?string $navigationGroup = 'Operación';
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?int    $navigationSort = 11;
    protected static ?string $navigationLabel = 'Radicados';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('numero')
                ->label('Número')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('modulo')
                ->label('Módulo')
                ->required()
                ->helperText('Ej: contrato, soporte, pqr…'),

            Forms\Components\Select::make('user_id')
                ->label('Usuario')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->native(false),

            Forms\Components\Placeholder::make('radicable_info')
                ->label('Radicable')
                ->content(fn ($record) =>
                    $record?->radicable
                        ? class_basename($record->radicable_type) . ' #' . $record->radicable_id
                        : '—'
                ),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->label('Radicado')->searchable()->sortable()->copyable(),

                Tables\Columns\TextColumn::make('modulo')
                    ->badge()->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario'),

                Tables\Columns\TextColumn::make('radicable_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('radicable_id')
                    ->label('ID')->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('modulo')->options([
                    'contrato' => 'Contrato',
                    'soporte'  => 'Soporte',
                    'pqr'      => 'PQR',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => RadicadoResource\Pages\ListRadicados::route('/'),
            'create' => RadicadoResource\Pages\CreateRadicado::route('/create'),
            'edit'   => RadicadoResource\Pages\EditRadicado::route('/{record}/edit'),
            // 'view' => RadicadoResource\Pages\ViewRadicado::route('/{record}'),
        ];
    }
}
