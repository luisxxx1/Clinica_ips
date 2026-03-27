<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'job_title',
        'ui_color',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |-------------------------------------------------------------------------- */

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Resultados de exámenes realizados por este especialista.
     */
    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Entradas clinicas registradas por este usuario.
     */
    public function clinicalHistories(): HasMany
    {
        return $this->hasMany(ClinicalHistory::class);
    }

    /* |--------------------------------------------------------------------------
    | Helpers & Accessors (Snake_DEV UX)
    |-------------------------------------------------------------------------- */

    /**
     * Genera las iniciales del usuario para el avatar si no tiene foto.
     * Uso: {{ auth()->user()->initials }}
     */
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: function () {
                $words = explode(' ', $this->name);
                return strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            }
        );
    }

    /**
     * Verifica múltiples roles a la vez.
     * Uso: if($user->hasAnyRole(['Médico', 'Psicólogo'])) ...
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array(strtolower($this->role->name), array_map('strtolower', $roles));
    }

    /**
     * Alias simple para verificar un solo rol.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && strtolower($this->role->name) === strtolower($roleName);
    }
}
