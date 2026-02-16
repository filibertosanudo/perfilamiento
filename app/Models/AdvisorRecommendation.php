<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvisorRecommendation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'advisor_id',
        'test_response_id',
        'recommendation_text',
        'created_at',
        'read',
    ];

    protected $casts = [
        'created_at' => 'date',
        'read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function testResponse(): BelongsTo
    {
        return $this->belongsTo(TestResponse::class);
    }
}
