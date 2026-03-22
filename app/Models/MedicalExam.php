<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MedicalExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'user_id',         // El médico/admin que inició el examen
        'requested_areas', // Array JSON
        'status',          // pendiente, en_proceso, completado
        'observations',
        'result_type',
    ];

    protected $casts = [
        'requested_areas' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |-------------------------------------------------------------------------- */

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * El usuario (profesional o admin) que creó el registro.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function results(): HasMany
    {
        // Vinculamos con MedicalResult (o ExamResult, verifica el nombre de tu modelo)
        return $this->hasMany(ExamResult::class, 'medical_exam_id');
    }

    /* |--------------------------------------------------------------------------
    | Scopes (Filtros)
    |-------------------------------------------------------------------------- */

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['pendiente', 'en_proceso']);
    }

    /* |--------------------------------------------------------------------------
    | Accessors & Helpers (Lógica de Negocio)
    |-------------------------------------------------------------------------- */

    /**
     * Determina si todas las áreas solicitadas tienen un resultado cargado.
     */
    public function getIsReadyAttribute(): bool
    {
        $requested = $this->requested_areas;

        if (empty($requested) || !is_array($requested)) {
            return false;
        }

        // Cargamos los slugs de las áreas que ya tienen resultados
        $completedAreas = $this->results->pluck('area')->toArray();

        foreach ($requested as $area) {
            // Normalizamos el nombre del área solicitada para comparar
            $slug = Str::slug($area, '_');
            if (!in_array($slug, $completedAreas)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calcula el porcentaje de avance basado en las áreas evaluadas.
     */
    public function getProgressPercentAttribute(): int
    {
        $totalAreas = count($this->requested_areas ?? []);
        if ($totalAreas === 0) return 0;

        $completedCount = $this->results->count();
        
        $percent = ($completedCount / $totalAreas) * 100;
        return (int) min($percent, 100);
    }

    /**
     * Verifica si un área específica (por nombre o slug) ya fue evaluada.
     */
    public function isAreaCompleted(string $areaName): bool
    {
        $slug = Str::slug($areaName, '_');
        
        // Usamos la colección cargada en memoria para evitar queries extra
        return $this->results->contains('area', $slug);
    }

    /**
     * Helper para obtener el color del badge de estado.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completado' => 'emerald',
            'en_proceso' => 'amber',
            default      => 'slate',
        };
    }
}