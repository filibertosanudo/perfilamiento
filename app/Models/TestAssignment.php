<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAssignment extends Model
{
    protected $fillable = [
        'test_id',
        'assigned_by',
        'user_id',
        'group_id',
        'institution_id',
        'assigned_at',
        'due_date',
        'active',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_date'    => 'date',
        'active'      => 'boolean',
    ];


    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Orientador que realizó la asignación
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Usuario individual asignado (nullable)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Grupo asignado (nullable)
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Institución asignada (nullable)
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Determina el tipo de asignación: 'user' | 'group' | 'institution'
     */
    public function getTargetTypeAttribute(): string
    {
        return match(true) {
            !is_null($this->user_id)        => 'user',
            !is_null($this->group_id)       => 'group',
            !is_null($this->institution_id) => 'institution',
            default                         => 'unknown',
        };
    }

    /**
     * Devuelve la entidad objetivo sin importar el tipo
     */
    public function getTargetAttribute(): Model|null
    {
        return match($this->target_type) {
            'user'        => $this->user,
            'group'       => $this->group,
            'institution' => $this->institution,
            default       => null,
        };
    }
}