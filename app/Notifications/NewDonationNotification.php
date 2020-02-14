<?php

namespace App\Notifications;

use App\Channels\GraphQLChannel;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class NewDonationNotification extends Notification implements ShouldQueue
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
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail', GraphQLChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->view(
            'emails.accept_donation',
            [
                'payer' => $this->transaction->payer,
                'payee' => $this->transaction->payee,
                'transaction' => $this->transaction,
                'accept_link' => route(
                    'capture',
                    [
                        'paymentId' => $this->transaction->transaction_data['paymentId'],
                        'accept' => true,
                    ]
                ),
                'decline_link' => route(
                    'capture',
                    [
                        'paymentId' => $this->transaction->transaction_data['paymentId'],
                        'decline' => true,
                    ]
                )
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Set default message
        $message = trans(
            'notifications.new_donation_notification',
            [
                'payer' => $this->transaction->payer->display_name,
                'expiresIn' => $this->transaction->updated_at->addDays(3)->format('F j \a\t g:i A')
            ]
        );

        if ($this->transaction->status == Transaction::STATUS_ACCEPTED) {
            $message = trans('notifications.new_donation_auto_accepted', [
               'payer' => $this->transaction->payer->display_name,
            ]);
        }

        if ($this->transaction->status == Transaction::STATUS_DECLINED) {
            $message = trans('notifications.new_donation_auto_declined', [
                'payer' => $this->transaction->payer->display_name,
            ]);
        }

        return [
            'type_name' => 'transaction',
            'message' => $message,
            'transaction_id' => $this->transaction->id,
            'user_id' => $this->transaction->payer->id,
        ];
    }

    public function toGraphql($notifiable)
    {
        $notification = DatabaseNotification::findOrFail($this->id);

        Subscription::broadcast('notificationCreated', $notification);
    }
}
