<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'test_id',
        'text',
        'order',
        'answer_type',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Test al que pertenece
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Opciones de respuesta
     */
    public function answerOptions(): HasMany
    {
        return $this->hasMany(AnswerOption::class)->orderBy('order');
    }

    /**
     * Detalles de respuestas de usuarios
     */
    public function responseDetails(): HasMany
    {
        return $this->hasMany(ResponseDetail::class);
    }
}