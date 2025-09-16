<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?string $navigationLabel = 'Usuarios';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([

            // === 1) Búsqueda externa (public.usuarios)
            Forms\Components\Fieldset::make('Búsqueda externa (por usr_codigo)')
                ->schema([
                    Forms\Components\TextInput::make('usr_codigo')
                        ->label('Código usuario (externo)')
                        ->placeholder('Ej: RT123')
                        ->required()
                        ->maxLength(50)
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('autocomplete')
                                ->label('Buscar')
                                ->icon('heroicon-m-magnifying-glass')
                                ->action(function (Get $get, Set $set) {
                                    $codigo = trim((string) $get('usr_codigo'));
                                    if ($codigo === '') return;

                                    // Consulta explícita al esquema public.usuarios
                                    $ext = DB::connection('pgsql')
                                        ->table(DB::raw('"public"."usuarios" as u'))
                                        ->select([
                                            'u.usr_codigo',
                                            'u.nombre_completo',
                                            'u.email',
                                            'u.activo',
                                            'u.grupo_cod',
                                            'u.documento',
                                            'u.tipodcto_cod',
                                        ])
                                        ->where('u.usr_codigo', $codigo)
                                        ->first();

                                    if (! $ext) {
                                        Notification::make()
                                            ->title('No encontrado')
                                            ->body("No existe usr_codigo {$codigo} en public.usuarios.")
                                            ->warning()
                                            ->send();
                                        return;
                                    }

                                    // Mapeo a campos locales (SIEMPRE, sin bloquear por TES/activo)
                                    $set('username',  $ext->usr_codigo ?? $codigo);
                                    $set('nombres',   $ext->nombre_completo ?? '');
                                    $set('email',     $ext->email ?? null);
                                    $set('is_active', (bool) ($ext->activo ?? false));

                                    // Solo informativos (no se guardan si tu modelo no los tiene)
                                    $set('grupo_cod_ext', $ext->grupo_cod ?? null);
                                    $set('documento_ext', $ext->documento ?? null);
                                    $set('tipodoc_ext',   $ext->tipodcto_cod ?? null);

                                    // Aviso (no bloqueante) si no es TES o no está activo
                                    if (! $ext->activo || ($ext->grupo_cod ?? null) !== 'TES') {
                                        $detalles = [];
                                        if (! $ext->activo) $detalles[] = 'inactivo';
                                        if (($ext->grupo_cod ?? null) !== 'TES') $detalles[] = "grupo {$ext->grupo_cod}";
                                        $detalleTxt = $detalles ? ' (' . implode(', ', $detalles) . ')' : '';

                                        Notification::make()
                                            ->title('Usuario externo encontrado')
                                            ->body("El usuario {$codigo} no es TES o no está activo{$detalleTxt}. Puedes crearlo igualmente.")
                                            ->warning()
                                            ->send();
                                    } else {
                                        Notification::make()
                                            ->title('Datos cargados')
                                            ->body("Usuario {$codigo} (TES, activo) cargado desde public.usuarios.")
                                            ->success()
                                            ->send();
                                    }
                                })
                        ),
                ])->columns(2),

            // === 2) Datos del usuario (local)
            Forms\Components\Section::make('Datos del usuario')
                ->schema([
                    Forms\Components\TextInput::make('username')
                        ->label('Usuario (login)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100),

                    Forms\Components\TextInput::make('nombres')
                        ->label('Nombre completo')
                        ->required()
                        ->maxLength(150),

                    Forms\Components\TextInput::make('email')
                        ->label('Correo')
                        ->email()
                        ->maxLength(190),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Activo')
                        ->default(true),

                    Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->label('Roles'),

                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => $state ?: null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->label('Password (si desea cambiar/crear)'),
                ])
                ->columns(2),

            // === 3) Datos externos (solo lectura) — plegable
            Forms\Components\Section::make('Datos externos (solo lectura)')
                ->schema([
                    Forms\Components\TextInput::make('grupo_cod_ext')
                        ->label('Grupo (externo)')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('documento_ext')
                        ->label('Documento (externo)')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('tipodoc_ext')
                        ->label('Tipo Doc (externo)')
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->columns(3)
                ->collapsed(),
        ])->columns(2);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('username')->label('Usuario')->searchable(),
            Tables\Columns\TextColumn::make('usr_codigo')->label('Código'),
            Tables\Columns\TextColumn::make('nombres')->label('Nombre completo')->searchable(),
            Tables\Columns\TextColumn::make('email')->label('Correo')->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TagsColumn::make('roles.name')->label('Roles'),
            Tables\Columns\IconColumn::make('is_active')->boolean()->label('Activo'),
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
            'index' => UserResource\Pages\ManageUsers::route('/'),
        ];
    }
}
