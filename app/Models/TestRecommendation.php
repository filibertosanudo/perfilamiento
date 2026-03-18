<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestRecommendation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'test_id',
        'min_range',
        'max_range',
        'result_category',
        'recommendation_text',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
