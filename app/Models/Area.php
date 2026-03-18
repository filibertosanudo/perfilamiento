<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
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
     * Todos los usuarios de esta área (cualquier rol)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Solo los orientadores de esta área (role_id = 2)
     */
    public function advisors(): HasMany
    {
        return $this->hasMany(User::class)->where('role_id', 2);
    }

    /**
     * Grupos que pertenecen a esta área
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Asignaciones de tests hechas a nivel de área completa
     */
    public function testAssignments(): HasMany
    {
        return $this->hasMany(TestAssignment::class);
    }
}