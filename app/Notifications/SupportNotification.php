<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;

class SupportNotification extends Notification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
            ->subject('Support '.env('APP_NAME'))
            ->greeting('Cher '.$this->name.', ')
            ->line('Votre demande a été reçu et sera traitée sous peu.')
            ->line('Ceci est un mail automatique, veuillez ne pas y répondre.')
            ->line('Votre plateforme d\'échange de monnaies avec une couverture sous-régionale.')
            ->line('Nous cultivons au jour le jour des valeurs pour mieux vous servir.')
            ->line('Cordialement '.(new HtmlString(''.env('APP_NAME').' . ')));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
