<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    protected $fillable = [
        'name',
        'type',
        'city',
        'address',
        'phone',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Todos los usuarios de esta institución (cualquier rol)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Solo los orientadores de esta institución (role_id = 2)
     */
    public function advisors(): HasMany
    {
        return $this->hasMany(User::class)->where('role_id', 2);
    }

    /**
     * Grupos que pertenecen a esta institución
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Asignaciones de tests hechas a nivel institución completa
     */
    public function testAssignments(): HasMany
    {
        return $this->hasMany(TestAssignment::class);
    }
}