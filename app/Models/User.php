<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// Filament
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',       // opcional si usas esta columna
    ];

    /**
     * Atributos ocultos en serializaci車n.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    /**
     * Control de acceso a paneles de Filament.
     * - admin  ↙ requiere rol 'admin'
     * - staff  ↙ permite 'admin' o 'empleado'
     * Si usas is_active y es false, bloquea acceso.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (($this->is_active ?? true) === false) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->hasRole('admin'),
            'staff' => $this->hasAnyRole(['admin', 'empleado']),
            default => false,
        };
    }
}
