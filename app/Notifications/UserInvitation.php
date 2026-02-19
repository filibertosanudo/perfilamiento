<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class UserInvitation extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'invitation.accept',
            now()->addHours(72),
            ['token' => $notifiable->invitation_token]
        );

        return (new MailMessage)
            ->subject('Bienvenido a ' . config('app.name'))
            ->greeting('¡Hola ' . $notifiable->first_name . '!')
            ->line('Has sido registrado en nuestro sistema de perfilamiento.')
            ->line('Para completar tu registro, por favor crea tu contraseña haciendo clic en el botón de abajo.')
            ->action('Crear mi contraseña', $url)
            ->line('Este enlace expirará en 72 horas.')
            ->line('Si no esperabas este correo, puedes ignorarlo.')
            ->salutation('Saludos, ' . config('app.name'));
    }
}