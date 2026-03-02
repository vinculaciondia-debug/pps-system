<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class CustomResetPassword extends BaseResetPassword
{
    /**
     * Construye el correo de recuperación.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Recuperación de Contraseña - PPS UNAH')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line(new HtmlString('<img src="' . asset('img/UNAH-version-horizontal.png') . '" alt="UNAH Logo" style="width: 150px; margin: 10px 0;">'))
            ->line('Recibiste este correo porque se solicitó un restablecimiento de contraseña para tu cuenta en el Sistema de Práctica Profesional Supervisada.')
            ->action('Restablecer Contraseña', url(route('password.reset', $this->token, false)))
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si no realizaste esta solicitud, no se requiere realizar ninguna acción.');
    }
}
