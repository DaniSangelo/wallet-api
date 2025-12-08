<?php

namespace App\Services;

use App\Contracts\Repositories\WalletRepositoryInterface;
use App\DTO\CreateWalletDTO;
use App\Models\Wallet;

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
}