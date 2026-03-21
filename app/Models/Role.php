<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color', 
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |-------------------------------------------------------------------------- */

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /* |--------------------------------------------------------------------------
    | Accessors & Helpers (Snake_DEV UX)
    |-------------------------------------------------------------------------- */

    /**
     * Accessor para estilos de Tailwind.
     * Uso en Blade: {{ $role->badge_style }}
     */
    protected function badgeStyle(): Attribute
    {
        return Attribute::make(
            get: function () {
                $color = $this->color ?: 'slate';
                return "bg-{$color}-50 text-{$color}-700 border-{$color}-200 px-2 py-0.5 rounded-full border text-xs font-medium";
            }
        );
    }

    /**
     * Verifica el nombre del rol.
     * Importante: Usamos 'hasName' para NO chocar con el método is() interno de Laravel.
     */
    public function hasName(string $roleName): bool
    {
        // strcasecmp devuelve 0 si son iguales (ignorando mayúsculas/minúsculas)
        return strcasecmp($this->name ?? '', $roleName) === 0;
    }
}