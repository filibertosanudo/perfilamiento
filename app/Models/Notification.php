<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Marcar como leída
     */
    public function markAsRead(): void
    {
        if (!$this->read) {
            $this->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Obtener ícono según tipo
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'test_assigned' => 'clipboard',
            'test_completed' => 'check-circle',
            'result_severe' => 'alert-triangle',
            'reminder' => 'clock',
            default => 'bell',
        };
    }

    /**
     * Obtener color según tipo
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'test_assigned' => 'blue',
            'test_completed' => 'green',
            'result_severe' => 'red',
            'reminder' => 'amber',
            default => 'gray',
        };
    }
}