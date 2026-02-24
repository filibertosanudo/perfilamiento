<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'test_response_id',
        'question_id',
        'answer_option_id',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    /**
     * Respuesta del test
     */
    public function testResponse(): BelongsTo
    {
        return $this->belongsTo(TestResponse::class);
    }

    /**
     * Pregunta respondida
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Opción seleccionada
     */
    public function answerOption(): BelongsTo
    {
        return $this->belongsTo(AnswerOption::class);
    }
}