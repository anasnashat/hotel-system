<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $client;
    /**
     * Create a new notification instance.
     */
    public function __construct($client)
    {
        //
        $this->client = $client;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Account Has Been Approved Successfully!')
            ->greeting('Hello, '. $this->client->name.', ')
            ->line('Congratulation! We are happy to inform you that your account has been approved.')
            ->action('You could Login Now', url('/login'))
            ->line('Thank you for registering with us! Hope You Have a great stay')
            ->line('Best Regards')
            ->line('Hotel Team');

        }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message'=>'Your Account Has Been Approved!',
            'client_id'=>$this->client->id,
        ];
    }
}
