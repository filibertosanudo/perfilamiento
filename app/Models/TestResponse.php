<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestResponse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'test_assignment_id',
        'user_id',
        'started_at',
        'finished_at',
        'completed',
        'numeric_result',
        'result_category',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'completed' => 'boolean',
        'numeric_result' => 'decimal:2',
    ];

    /**
     * Asignación del test
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TestAssignment::class, 'test_assignment_id');
    }

    /**
     * Usuario que respondió
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Detalles de las respuestas
     */
    public function details(): HasMany
    {
        return $this->hasMany(ResponseDetail::class);
    }

    /**
     * Progreso del test (porcentaje)
     */
    public function getProgressAttribute(): float
    {
        $totalQuestions = $this->assignment->test->total_questions;
        $answeredQuestions = $this->details()->count();

        return $totalQuestions > 0 ? ($answeredQuestions / $totalQuestions) * 100 : 0;
    }

    /**
     * Verificar si está en progreso
     */
    public function getInProgressAttribute(): bool
    {
        return $this->started_at && !$this->completed;
    }

    /**
     * Tiempo transcurrido desde que empezó (en horas)
     */
    public function getElapsedHoursAttribute(): ?float
    {
        if (!$this->started_at) {
            return null;
        }

        $end = $this->finished_at ?? now();
        return $this->started_at->diffInHours($end, true);
    }
}