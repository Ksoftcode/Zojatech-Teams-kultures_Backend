<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailNotification extends Notification
{
    use Queueable;
    // private $EmailNotificationData;
    public $url;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( string $url)
    {
        // $this->EmailNotificationData = $EmailNotificationData;
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
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //                 ->line($this->EmailNotificationData['body'])
    //                 ->action($this->EmailNotificationData['EmailNotificationText'], $this->EmailNotificationData['url'])
    //                 ->line($this->EmailNotificationData['Thank you for using our application!']);
    // }
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
        ->line('Thanks your registriation was suceessful ')
        ->action('Click to verify your email', $this->url)
        ->line('Thank you for using our application!');

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
