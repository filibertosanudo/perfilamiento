<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    protected $fillable = [
        'name',
        'description',
        'objective',
        'estimated_time',
        'minimum_retest_time',
        'active'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function assignments()
    {
        return $this->hasMany(TestAssignment::class);
    }
}

