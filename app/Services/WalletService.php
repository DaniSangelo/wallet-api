<?php

namespace App\Services;

use App\Contracts\Repositories\TransactionRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\WalletRepositoryInterface;
use App\DTO\CreateTransactionDTO;
use App\DTO\CreateWalletDTO;
use App\DTO\TransferAmountDTO;
use App\Models\Wallet;
use App\TransactionTypeEnum;
use Exception;

class WalletService
{
    public WalletRepositoryInterface $walletRepository;
    public UserRepositoryInterface $userRepository;
    public TransactionRepositoryInterface $transactionRepository;

    public function __construct(
        WalletRepositoryInterface $walletRepository,
        UserRepositoryInterface $userRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->walletRepository = $walletRepository;
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function create(CreateWalletDTO $data)
    {
        $wallet = $this->walletRepository->alreadyHasWallet($data->user_id);
        if (!blank($wallet)) {
            return $this->walletRepository->restore($wallet);
        }

        return $this->walletRepository->create($data);
    }

    public function getBalance(int $userId): ?Wallet
    {
        return $this->walletRepository->getBalance($userId);
    }

    public function addBalance(int $userId, float $amount): ?Wallet
    {
        $wallet = $this->walletRepository->alreadyHasWallet($userId);
        if (blank($wallet) || $wallet->trashed()) {
            throw new Exception('Wallet does not exist or is deleted');
        }
        $transactionDto = CreateTransactionDTO::createFromArray([
            'user_id_to' => $userId,
            'wallet_id' => $wallet->id,
            'user_id' => $userId,
            'type' => TransactionTypeEnum::CREDIT,
            'amount' => $amount,
        ]);
        $this->transactionRepository->addTransaction($transactionDto);
        return $this->walletRepository->updateBalance($wallet, $amount);
        
    }

    public function withdraw(int $userId, float $amount): ?Wallet
    {
        $wallet = $this->walletRepository->alreadyHasWallet($userId);
        if (!$this->hasEnoughtBalance($wallet, $amount)) {
            throw new Exception('Not enough balance');
        }

        $transactionDto = CreateTransactionDTO::createFromArray([
            'user_id_to' => $userId,
            'wallet_id' => $wallet->id,
            'user_id' => $userId,
            'type' => TransactionTypeEnum::WITHDRAW,
            'amount' => $amount,
        ]);
        $this->transactionRepository->addTransaction($transactionDto);
        return $this->walletRepository->updateBalance($wallet, $amount * -1);
    }

    private function hasEnoughtBalance(Wallet $wallet, $amount): bool
    {
        if (blank($wallet) || $wallet->trashed()) {
            throw new Exception('Wallet does not exist or is deleted');
        }

        return $wallet->balance >= $amount;
    }

    public function transfer(TransferAmountDTO $data)
    {
        $user_id_to = $this->userRepository->getByEmail($data->to_email)->id;

        $wallet = $this->walletRepository->alreadyHasWallet($user_id_to);
        if (blank($wallet) || $wallet->trashed()) {
            throw new Exception('Destinatary wallet does not exist or is deleted');
        }

        $fromWallet = $this->walletRepository->alreadyHasWallet($data->user_id);

        if(!$this->hasEnoughtBalance($fromWallet, $data->amount)) {
            throw new Exception('Not enough balance');
        }

        $fromWallet = $this->walletRepository->updateBalance($fromWallet, $data->amount * -1);
        $transactionDto = CreateTransactionDTO::createFromArray([
            'user_id_to' => $user_id_to,
            'wallet_id' => $wallet->id,
            'user_id' => $wallet->user_id,
            'type' => TransactionTypeEnum::DEBIT,
            'amount' => $data->amount,
        ]);
        $this->transactionRepository->addTransaction($transactionDto);

        $wallet = $this->walletRepository->updateBalance($wallet, $data->amount);
        return;
    }

    public function transactions(int $userId)
    {
        return $this->walletRepository->transactions($userId);
    }
}
