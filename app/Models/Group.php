<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'creator_id',
        'created_at',
        'active',
    ];

    protected $casts = [
        'created_at' => 'date',
        'active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('joined_at');
    }

    public function testAssignments()
    {
        return $this->hasMany(TestAssignment::class);
    }

}
