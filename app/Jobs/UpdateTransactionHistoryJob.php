<?php

namespace App\Jobs;

use App\Contracts\Repositories\TransactionRepositoryInterface;
use App\Contracts\Repositories\WalletRepositoryInterface;
use App\DTO\CreateTransactionDTO;
use App\Models\Wallet;
use App\TransactionTypeEnum;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateTransactionHistoryJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Wallet $walletFrom,
        public int $user_id_to,
        public Wallet $walletTo,
        public float $amount,
        public WalletRepositoryInterface $walletRepository,
        public TransactionRepositoryInterface $transactionRepository
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->walletFrom = $this->walletRepository->updateBalance($this->walletFrom, $this->amount * -1);
            $transactionDto = CreateTransactionDTO::createFromArray([
                'user_id_to' => $this->user_id_to,
                'wallet_id' => $this->walletFrom->id,
                'user_id' => $this->walletFrom->user_id,
                'type' => TransactionTypeEnum::DEBIT,
                'amount' => $this->amount,
            ]);
            $this->transactionRepository->addTransaction($transactionDto);

            $this->walletRepository->updateBalance($this->walletTo, $this->amount);
            Http::post(config('app.webhook_url'), ['success' => true, 'message' => 'Transfer successful', 'data' => null]);
        } catch (Exception $e) {
            Log::error('Error on update transaction history', ['message' => $e->getMessage()]);
        }
    }
}
