<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IntentoDeAcceso extends Notification
{
     use Queueable;

    protected $ip;
    protected $userAgent;

    /**
     * Create a new notification instance.
     */
    public function __construct($ip, $userAgent)
    {
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Intento de acceso detectado')
                    ->line('Se detectó un intento de acceso al sistema.')
                    ->line('IP: ' . $this->ip)
                    ->line('Agente de usuario: ' . $this->userAgent)
                    ->line('Si no fuiste tú, por favor toma medidas.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
        ];
    }
}
