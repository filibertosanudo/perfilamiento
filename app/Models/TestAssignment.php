<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'due_date' => 'date',
        'active' => 'boolean',
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

    public function responses(): HasMany
    {
        return $this->hasMany(TestResponse::class);
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
    
    public function getAffectedUsersAttribute()
    {
        if ($this->user_id) {
            return collect([$this->user]);
        }

        if ($this->group_id) {
            return $this->group->users;
        }

        if ($this->institution_id) {
            return User::where('institution_id', $this->institution_id)
                ->where('role_id', 3)
                ->where('active', true)
                ->get();
        }

        return collect();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Obtener tiempo restante formateado
     */
    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->due_date) {
            return null;
        }

        $now = now();
        $dueDate = $this->due_date->endOfDay(); // Hasta el final del día

        if ($dueDate->isPast()) {
            $diffInDays = floor(abs($now->diffInDays($dueDate)));
            return "Vencido hace " . ($diffInDays == 0 ? 'hoy' : $diffInDays . ' día' . ($diffInDays != 1 ? 's' : ''));
        }

        $diffInMinutes = $now->diffInMinutes($dueDate);
        $diffInHours = $now->diffInHours($dueDate);
        $diffInDays = floor($now->diffInDays($dueDate));

        // Más de 1 día
        if ($diffInDays > 0) {
            return "Faltan " . $diffInDays . ' día' . ($diffInDays != 1 ? 's' : '');
        }

        // Menos de 1 día pero más de 1 hora
        if ($diffInHours > 0) {
            $remainingMinutes = $diffInMinutes % 60;
            return "Faltan " . $diffInHours . 'h ' . $remainingMinutes . 'min';
        }

        // Menos de 1 hora
        return "Faltan " . $diffInMinutes . ' minutos';
    }

    /**
     * Color del indicador según urgencia
     */
    public function getUrgencyColorAttribute(): string
    {
        if (!$this->due_date) {
            return 'gray';
        }

        if ($this->is_expired) {
            return 'red';
        }

        $daysRemaining = floor(now()->diffInDays($this->due_date, false));

        if ($daysRemaining <= 1) {
            return 'red';
        } elseif ($daysRemaining <= 3) {
            return 'amber';
        } else {
            return 'gray';
        }
    }
}