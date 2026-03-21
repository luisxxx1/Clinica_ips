<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class MedicalExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'user_id',
        'requested_areas',
        'status', 
        'observations',
        'result_type',
    ];

    protected $casts = [
        'requested_areas' => 'array',
        'created_at' => 'datetime', // Útil para filtros de fechas en reportes
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |-------------------------------------------------------------------------- */

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    /* |--------------------------------------------------------------------------
    | Scopes (Filtros de Bandeja)
    |-------------------------------------------------------------------------- */

    public function scopeForArea(Builder $query, string $area): Builder
    {
        // Se asegura de que la consulta busque correctamente dentro del JSON
        return $query->whereJsonContains('requested_areas', strtolower($area));
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['pendiente', 'en_proceso']);
    }

    /* |--------------------------------------------------------------------------
    | Lógica de Negocio (Helpers)
    |-------------------------------------------------------------------------- */

    /**
     * Calcula el porcentaje de avance del circuito médico.
     * Útil para barras de progreso en la interfaz.
     */
    public function getProgressPercentAttribute(): int
    {
        $total = count($this->requested_areas ?? []);
        if ($total === 0) return 0;

        $completed = $this->results()->count();
        return (int) (($completed / $total) * 100);
    }

    /**
     * Verifica si todas las áreas solicitadas ya tienen un resultado cargado.
     */
    public function checkCompletion(): bool
    {
        $requested = collect($this->requested_areas)->map(fn($a) => strtolower($a))->sort()->values();
        $completed = $this->results()->pluck('area')->map(fn($a) => strtolower($a))->sort()->values();

        // Si lo que se pidió es igual a lo que hay en la tabla de resultados
        return $requested->every(fn($area) => $completed->contains($area));
    }

    /**
     * Verifica si un área específica ya fue evaluada.
     */
    public function isAreaCompleted(string $areaName): bool
    {
        // Usamos una comparación simple de strings normalizados
        return $this->results->contains('area', strtolower($areaName));
    }
}