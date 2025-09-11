<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages; 
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $navigationGroup = 'Gestión';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->label('Correo electrónico')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('password')
                ->label('Contraseña')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->required(fn (string $context): bool => $context === 'create')
                ->dehydrated(fn ($state) => filled($state))
                ->maxLength(255),

            Forms\Components\Select::make('roles')
                ->label('Rol')
                ->multiple(false) // solo uno por usuario
                ->relationship('roles', 'name')
                ->preload()
                ->searchable()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Nombre')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Correo')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Rol')->badge(),
                Tables\Columns\TextColumn::make('created_at')->label('Creado')->dateTime('d/m/Y H:i'),
            ])
            ->filters([])
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
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
