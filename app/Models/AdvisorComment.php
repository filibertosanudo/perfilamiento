<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvisorComment extends Model
{
    protected $fillable = [
        'user_id',
        'advisor_id',
        'test_response_id',
        'body',
        'type',
        'is_private',
        'flag_follow_up',
    ];

    protected $casts = [
        'is_private'     => 'boolean',
        'flag_follow_up' => 'boolean',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /** El usuario al que pertenece el comentario */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** El orientador que escribió el comentario */
    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    /** El resultado de test relacionado (opcional) */
    public function testResponse(): BelongsTo
    {
        return $this->belongsTo(TestResponse::class);
    }

    /**
     * Etiqueta legible del tipo
     */
    public function typeLabel(): string
    {
        return match($this->type) {
            'note'       => 'Nota',
            'follow_up'  => 'Seguimiento',
            'alert'      => 'Alerta',
            default      => 'Nota',
        };
    }

    /**
     * Clase de color Tailwind según tipo
     */
    public function typeColor(): string
    {
        return match($this->type) {
            'note'       => 'blue',
            'follow_up'  => 'amber',
            'alert'      => 'red',
            default      => 'gray',
        };
    }
}
