<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\WalletRepositoryInterface;
use App\DTO\CreateWalletDTO;
use App\DTO\TransferAmountDTO;
use App\Models\Wallet;
use Exception;

class WalletService
{
    public WalletRepositoryInterface $walletRepository;
    public UserRepositoryInterface $userRepository;

    public function __construct(WalletRepositoryInterface $walletRepository, UserRepositoryInterface $userRepository)
    {
        $this->walletRepository = $walletRepository;
        $this->userRepository = $userRepository;
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

    public function transfer(TransferAmountDTO $data)
    {
        $to_user_id = $this->userRepository->getByEmail($data->to_email)->id;

        $wallet = $this->walletRepository->alreadyHasWallet($to_user_id);
        if (blank($wallet) || $wallet->trashed()) {
            throw new Exception('Destinatary wallet does not exist or is deleted');
        }

        $fromWallet = $this->walletRepository->alreadyHasWallet($data->user_id);

        if(!$this->hasEnoughtBalance($fromWallet, $data->amount)) {
            throw new Exception('Not enough balance');
        }

        $fromWallet = $this->walletRepository->updateBalance($fromWallet, $data->amount * -1);
        //todo: update transaction history
        $wallet = $this->walletRepository->updateBalance($wallet, $data->amount);
        //todo: update transaction history
        return;
    }
}
