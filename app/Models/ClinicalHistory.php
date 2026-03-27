<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'user_id',
        'area',
        'title',
        'entry',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
