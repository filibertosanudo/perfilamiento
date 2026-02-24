<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnswerOption extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'question_id',
        'text',
        'weight',
        'order',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'order' => 'integer',
    ];

    /**
     * Pregunta a la que pertenece
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Respuestas de usuarios que eligieron esta opción
     */
    public function responseDetails(): HasMany
    {
        return $this->hasMany(ResponseDetail::class);
    }
}