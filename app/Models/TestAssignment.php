<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAssignment extends Model
{
    protected $fillable = [
        'test_id',
        'user_id',
        'group_id',
        'assigned_by',
        'assigned_at',
        'due_date',
        'active',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_date' => 'date',
        'active' => 'boolean',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
