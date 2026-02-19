<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use App\Rules\SecurePassword;

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
            'password' => ['required', 'confirmed', new SecurePassword()],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $this->user->update([
            'password' => Hash::make($this->password),
            'invitation_accepted_at' => now(),
            'invitation_token' => null,
            'active' => true,
            'email_verified_at' => now(),
        ]);

        \Log::info('Usuario activó su cuenta', [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
        ]);

        Auth::login($this->user);

        session()->flash('status', '¡Contraseña creada exitosamente!');
        
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.accept-invitation')->layout('layouts.guest');
    }
}