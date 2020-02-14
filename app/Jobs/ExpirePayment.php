<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Notifications\ExpiredDonationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class ExpirePayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->transaction->status = Transaction::STATUS_EXPIRED;
        $this->transaction->save();

        // Notify payee about expired donation
        $this->transaction->payee->notify(new ExpiredDonationNotification($this->transaction));
    }
}
