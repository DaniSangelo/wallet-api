<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\WalletRepositoryInterface;
use App\DTO\CreateWalletDTO;
use App\Models\Transaction;
use App\Models\Wallet;

class WalletRepository implements WalletRepositoryInterface
{
    public function create(CreateWalletDTO $data)
    {
        return Wallet::create($data->toArray());
    }

    public function getWalletByUser(int $userId): ?Wallet
    {
        return Wallet::where('user_id', $userId)->withTrashed()->first();
    }

    public function restore(Wallet $wallet): Wallet
    {
        $wallet->restore();
        return $wallet->refresh();
    }

    public function getBalance(int $userId): ?Wallet
    {
        return Wallet::where('user_id', $userId)->select('balance')->first();
    }

    public function updateBalance(Wallet $wallet, float $amount): Wallet
    {
        $wallet->balance += $amount;
        $wallet->save();
        return $wallet->refresh();
    }

    public function transactions(int $userId): mixed
    {
        return Transaction::whereHas('wallet', function ($query) use ($userId) {
            $query->where('user_id', '=', $userId);
        })->paginate()->items();
    }
}
