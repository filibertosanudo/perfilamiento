<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'role_id',
        'area_id',
        'first_name',
        'last_name',
        'second_last_name',
        'email',
        'password',
        'birth_date',
        'phone',
        'registered_at',
        'active',
        'invitation_token',
        'invitation_sent_at',
        'invitation_accepted_at',
        'failed_login_attempts',
        'locked_until',         
        'last_login_at',        
        'last_login_ip',        
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'registered_at'     => 'datetime',
            'birth_date'        => 'date',
            'active'            => 'boolean',
            'password'          => 'hashed',
            'invitation_sent_at'      => 'datetime',
            'invitation_accepted_at'  => 'datetime',
            'locked_until'            => 'datetime',
            'last_login_at'           => 'datetime',
        ];
    }

    // Helper para verificar si ya aceptó la invitación
    public function hasAcceptedInvitation(): bool
    {
        return !is_null($this->invitation_accepted_at);
    }

    // Helper para verificar si el token de invitación expiró
    public function invitationExpired(): bool
    {
        if (!$this->invitation_sent_at) {
            return true;
        }
        return $this->invitation_sent_at->addHours(72)->isPast();
    }

    /**
     * Rol del usuario
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Área/Facultad a la que pertenece (nullable para admin global)
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Grupos en los que participa este usuario
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user')
                    ->withPivot('joined_at');
    }

    /**
     * Grupos que este orientador administra (como creator)
     */
    public function managedGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'creator_id');
    }

    /**
     * Tests asignados individualmente a este usuario
     */
    public function testAssignments(): HasMany
    {
        return $this->hasMany(TestAssignment::class);
    }

    /**
     * Tests que este orientador ha asignado a otros
     */
    public function assignedTests(): HasMany
    {
        return $this->hasMany(TestAssignment::class, 'assigned_by');
    }


    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isAdvisor(): bool
    {
        return $this->role_id === 2;
    }

    public function isRegularUser(): bool
    {
        return $this->role_id === 3;
    }


    /**
     * Devuelve los orientadores de este usuario derivados de sus grupos.
     * Un usuario puede tener varios orientadores (uno por grupo).
     */
    public function getAdvisors()
    {
        return User::whereIn('id',
            $this->groups()->pluck('creator_id')
        )->get();
    }

    /**
     * Verifica si un orientador específico puede asignarle tests a este usuario.
     * Solo puede si el usuario pertenece a algún grupo que el orientador administra.
     */
    public function canBeAssignedByAdvisor(int $advisorId): bool
    {
        return $this->groups()
                    ->where('creator_id', $advisorId)
                    ->exists();
    }

    /**
     * Verifica si el usuario tiene al menos un grupo asignado.
     * Sin grupo no puede recibir tests.
     */
    public function hasGroup(): bool
    {
        return $this->groups()->exists();
    }

    // Helpers de seguridad y bloqueo de cuenta

    /**
     * Verifica si la cuenta está bloqueada
     */
    public function isLocked(): bool
    {
        if (!$this->locked_until) {
            return false;
        }
        
        if ($this->locked_until->isPast()) {
            $this->update([
                'locked_until' => null,
                'failed_login_attempts' => 0,
            ]);
            return false;
        }
        
        return true;
    }

    /**
     * Incrementa intentos fallidos y bloquea si es necesario
     */
    public function incrementFailedAttempts(): void
    {
        $attempts = $this->failed_login_attempts + 1;
        
        $data = ['failed_login_attempts' => $attempts];
        
        if ($attempts >= 5) {
            $data['locked_until'] = now()->addMinutes(15);
        }
        
        $this->update($data);
    }

    /**
     * Resetea intentos fallidos después de login exitoso
     */
    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    /**
     * Obtener sesiones activas del usuario
     */
    public function activeSessions()
    {
        return \DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->get();
    }

    /**
     * Cerrar todas las sesiones excepto la actual
     */
    public function logoutOtherDevices()
    {
        \DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('id', '!=', session()->getId())
            ->delete();
    }

    /**
     * Respuestas de tests del usuario
     */
    public function testResponses(): HasMany
    {
        return $this->hasMany(TestResponse::class);
    }

    /**
     * Comentarios privados del orientador sobre este usuario
     */
    public function advisorComments(): HasMany
    {
        return $this->hasMany(\App\Models\AdvisorComment::class);
    }
}