<?php

namespace App\Notifications;

use App\Channels\GraphQLChannel;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class ExpiredDonationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', GraphQLChannel::class];
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
            'type_name' => 'transaction',
            'message' => trans(
                'notifications.donation_expired',
                [
                    'payer' => $this->transaction->payer->display_name,
                ]
            ),
            'transaction_id' => $this->transaction->id,
            'user_id' => $this->transaction->payer->id,
        ];
    }

    public function toGraphql($notifiable)
    {
        $notification = DatabaseNotification::findOrFail($this->id);

        Subscription::broadcast('donationExpired', $this->transaction);
        Subscription::broadcast('notificationCreated', $notification);
    }
}
