<?php

namespace App\Listeners;

use App\Events\UpdateTransactionHistoryEvent;
use App\Jobs\UpdateTransactionHistoryJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateTransactionHistoryListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UpdateTransactionHistoryEvent $event): void
    {
        UpdateTransactionHistoryJob::dispatch(
            $event->walletFrom,
            $event->user_id_to,
            $event->walletTo,
            $event->amount,
            $event->walletRepository,
            $event->transactionRepository,
        );
    }
}
