<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class CustomVerifyEmail extends BaseVerifyEmail
{
    /**
     * Construye el correo de verificación.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verificación de Correo Electrónico - PPS UNAH')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line(new HtmlString('<img src="' . asset('img/UNAH-version-horizontal.png') . '" alt="UNAH Logo" style="width: 150px; margin: 10px 0;">'))
            ->line('Gracias por registrarte en el Sistema de Práctica Profesional Supervisada de la UNAH.')
            ->line('Antes de continuar, necesitamos que confirmes tu dirección de correo electrónico.')
            ->action('Verificar Correo', $verificationUrl)
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si no creaste una cuenta, no se requiere realizar ninguna acción.');
    }

    /**
     * Genera el enlace de verificación firmado.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
