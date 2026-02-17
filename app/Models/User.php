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
        'institution_id',
        'first_name',
        'last_name',
        'second_last_name',
        'email',
        'password',
        'birth_date',
        'phone',
        'registered_at',
        'active',
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
        ];
    }


    /**
     * Rol del usuario
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Institución a la que pertenece (nullable para admin global)
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
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
}