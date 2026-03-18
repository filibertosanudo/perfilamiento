<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'objective',
        'estimated_time',
        'minimum_retest_time',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'estimated_time' => 'integer',
        'minimum_retest_time' => 'integer',
    ];

    /**
     * Preguntas del test
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Asignaciones de este test
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TestAssignment::class);
    }

    /**
     * Total de preguntas
     */
    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions()->count();
    }

    /**
     * Puntaje máximo posible del test
     */
    public function getMaxScoreAttribute(): float
    {
        return $this->questions()
            ->with('answerOptions')
            ->get()
            ->sum(function ($question) {
                return $question->answerOptions->max('weight') ?? 0;
            });
    }
}