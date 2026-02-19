<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ManageSessions extends Component
{
    public $showConfirmPassword = false;
    public $password = '';
    public $sessions = [];

    public function mount()
    {
        $this->loadSessions();
    }

    public function loadSessions()
    {
        $user = Auth::user();
        $currentSessionId = session()->getId();

        $sessions = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $this->parseUserAgent($session->user_agent),
                    'last_active' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'is_current' => $session->id === $currentSessionId,
                ];
            });

        $this->sessions = $sessions->toArray();
    }

    public function confirmLogoutOtherDevices()
    {
        $this->showConfirmPassword = true;
        $this->password = '';
    }

    public function logoutOtherDevices()
    {
        // Validar contraseña actual
        if (!Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => ['La contraseña es incorrecta.'],
            ]);
        }

        $user = Auth::user();
        
        // Cerrar todas las sesiones excepto la actual
        $user->logoutOtherDevices();

        \Log::info('Usuario cerró sesiones en otros dispositivos', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
        ]);

        $this->showConfirmPassword = false;
        $this->password = '';
        $this->loadSessions();

        session()->flash('message', 'Has cerrado sesión en todos los demás dispositivos.');
    }

    public function cancelLogout()
    {
        $this->showConfirmPassword = false;
        $this->password = '';
    }

    /**
     * Parsear user agent para mostrar dispositivo y navegador
     */
    private function parseUserAgent($userAgent)
    {
        // Detectar navegador
        if (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } else {
            $browser = 'Desconocido';
        }

        // Detectar sistema operativo
        if (preg_match('/Windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac OS X/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iPhone|iPad/i', $userAgent)) {
            $os = 'iOS';
        } else {
            $os = 'Desconocido';
        }

        return $browser . ' en ' . $os;
    }

    public function render()
    {
        return view('livewire.profile.manage-sessions');
    }
}