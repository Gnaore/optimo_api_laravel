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


class UserProfileLockNotification extends Notification
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $name;

    private $state;

    /**
     * UserProfilLockNotification constructor.
     * @param $name
     * @param $state
     */
    public function __construct($name, $state)
    {
        $this->name = $name;
        $this->state =  $state;
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

        $notif =  "";
        $app = env('APP_NAME');
        if($this->state === 'DISABLED'){
            $notif =  "Félicitation ! Votre compte a été réactivé avec succès.";
        }elseif ($this->state === 'ENABLED'){
            $notif =  "Votre compte a été bloqué pour non respect des conditions d'utilisations de $app. Veuillez contacter le support pour une prise en charge.";
        }

        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
            ->subject('Etat de compte - '.env('APP_NAME'))
            ->greeting('Cher '.$this->name.', ')
            ->line($notif)
            ->line('Merci pour votre fidélité.');
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
