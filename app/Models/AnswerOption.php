<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnswerOption extends Model
{
    protected $fillable = [
        'question_id',
        'text',
        'weight',
        'order',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
