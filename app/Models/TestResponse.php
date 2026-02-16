<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestResponse extends Model
{
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
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(TestAssignment::class, 'test_assignment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ResponseDetail::class);
    }
}
