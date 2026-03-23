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
        'user_id',         // ✅ CORRECCIÓN: Cambiado de 'created_by' a 'user_id'
        'requested_areas',
        'status',
        'observations',
        'result_type',
    ];

    protected $casts = [
        'requested_areas' => 'array',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
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
        // ✅ CORRECCIÓN: Apuntando a la columna real 'user_id'
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Resultados de cada área médica.
     */
    public function results(): HasMany
    {
        // Si tu modelo se llama MedicalResult, cámbialo aquí
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
    | Accessors & Helpers (Mantengo tu lógica igual)
    |-------------------------------------------------------------------------- */

    public function getIsReadyAttribute(): bool
    {
        $requested = $this->requested_areas;
        if (empty($requested) || !is_array($requested)) return false;

        $completedAreas = $this->results
            ->pluck('area')
            ->map(fn($a) => Str::slug($a, '_'))
            ->toArray();

        foreach ($requested as $area) {
            $slug = Str::slug($area, '_');
            if (!in_array($slug, $completedAreas)) return false;
        }

        return true;
    }

    public function getProgressPercentAttribute(): int
    {
        $totalAreas = count($this->requested_areas ?? []);
        if ($totalAreas === 0) return 0;
        $completedCount = $this->results->count();
        return (int) min(($completedCount / $totalAreas) * 100, 100);
    }

    public function isAreaCompleted(string $areaName): bool
    {
        $slug = Str::slug($areaName, '_');
        return $this->results->contains('area', $slug);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'completado' => 'emerald',
            'en_proceso' => 'amber',
            default      => 'slate',
        };
    }
}
