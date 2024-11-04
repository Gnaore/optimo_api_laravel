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

class PasswordResetVerification extends Notification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $url;
    private $name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($url, $name)
    {
        //
        $this->name = $name;
        $this->url = $url;
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
            ->subject('Réinitialisation De Mot De Passe')
            ->greeting('Cher '.$this->name.', ')
            ->line('Veuillez cliquer sur le lien suivant pour réinitialiser votre mot de passe : ')
            ->action('Continuer', $this->url);

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
