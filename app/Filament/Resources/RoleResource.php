<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon  = 'heroicon-o-lock-closed';
    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?string $navigationLabel = 'Roles';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre del rol')
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\Select::make('permissions')
                ->label('Permisos')
                ->options(Permission::query()->pluck('name', 'id'))
                ->multiple()
                ->preload()
                ->relationship('permissions', 'name'),
        ])->columns(2);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Rol')->searchable(),
                Tables\Columns\BadgeColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('# Permisos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        // 👇 Reemplaza las páginas faltantes por el "Manage" de un resource simple
        return [
            'index' => RoleResource\Pages\ManageRoles::route('/'),
        ];
    }
}
