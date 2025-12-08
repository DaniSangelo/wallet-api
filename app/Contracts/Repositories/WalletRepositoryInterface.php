<?php

namespace App\Contracts\Repositories;

use App\DTO\CreateWalletDTO;
use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function create(CreateWalletDTO $data);
    public function alreadyHasWallet(int $userId): ?Wallet;
    public function restore(Wallet $wallet): Wallet;
    public function getBalance(int $userId): ?Wallet;
    public function updateBalance(Wallet $wallet, float $amount): Wallet;
}
