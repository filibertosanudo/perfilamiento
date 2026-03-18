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
        'area_id',
        'creator_id',
        'name',
        'description',
        'active',
    ];

    protected $casts = [
        'created_at' => 'date',
        'active'     => 'boolean',
    ];

    /**
     * Área a la que pertenece este grupo (obligatorio)
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Orientador que administra este grupo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Usuarios que pertenecen a este grupo
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')
                    ->withPivot('joined_at');
    }

    /**
     * Asignaciones de tests hechas a este grupo
     */
    public function testAssignments(): HasMany
    {
        return $this->hasMany(TestAssignment::class);
    }
}