<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AcceptInvitation extends Component
{
    public User $user;
    public bool $expired = false;
    public bool $already_accepted = false;

    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->user = User::where('invitation_token', $token)->firstOrFail();

        if ($this->user->hasAcceptedInvitation()) {
            $this->already_accepted = true;
        } elseif ($this->user->invitationExpired()) {
            $this->expired = true;
        }
    }

    public function submit()
    {
        if ($this->expired || $this->already_accepted) {
            return;
        }

        $this->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $this->user->update([
            'password' => Hash::make($this->password),
            'invitation_accepted_at' => now(),
            'invitation_token' => null,
            'active' => true,
            'email_verified_at' => now(),
        ]);

        Auth::login($this->user);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.accept-invitation')->layout('layouts.guest');
    }
}