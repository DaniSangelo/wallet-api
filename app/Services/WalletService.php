<?php

namespace App\Services;

use App\Contracts\Repositories\WalletRepositoryInterface;
use App\DTO\CreateWalletDTO;
use App\Models\Wallet;
use Exception;

class WalletService
{
    public WalletRepositoryInterface $walletRepository;

    public function __construct(WalletRepositoryInterface $walletRepository)
    {
        $this->walletRepository = $walletRepository;
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

        return $this->walletRepository->updateBalance($wallet, $amount);
        //todo: update transaction history
    }

    public function withdraw(int $userId, float $amount): ?Wallet
    {
        $wallet = $this->walletRepository->alreadyHasWallet($userId);
        if (!$this->hasEnoughtBalance($wallet, $amount)) {
            throw new Exception('Not enough balance');
        }

        return $this->walletRepository->updateBalance($wallet, $amount * -1);
        //todo: update transaction history
    }

    private function hasEnoughtBalance(Wallet $wallet, $amount): bool
    {
        if (blank($wallet) || $wallet->trashed()) {
            throw new Exception('Wallet does not exist or is deleted');
        }

        return $wallet->balance >= $amount;
    }
}
