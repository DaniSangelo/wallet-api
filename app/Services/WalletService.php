<?php

namespace App\Services;

use App\Contracts\Repositories\TransactionRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\WalletRepositoryInterface;
use App\DTO\CreateTransactionDTO;
use App\DTO\CreateWalletDTO;
use App\DTO\TransferAmountDTO;
use App\Exceptions\CustomException;
use App\Models\Wallet;
use App\TransactionTypeEnum;
use Exception;
use Symfony\Component\HttpFoundation\Response;

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
            throw new CustomException('Wallet does not exist or is deleted', Response::HTTP_BAD_REQUEST);
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
            throw new CustomException('Not enough balance', Response::HTTP_BAD_REQUEST);
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
            throw new CustomException('Wallet does not exist or is deleted', Response::HTTP_BAD_REQUEST);
        }

        return $wallet->balance >= $amount;
    }

    public function transfer(TransferAmountDTO $data)
    {
        $user_id_to = $this->userRepository->getByEmail($data->to_email)->id;

        $wallet = $this->walletRepository->alreadyHasWallet($user_id_to);
        if (blank($wallet) || $wallet->trashed()) {
            throw new CustomException('Destinatary wallet does not exist or is deleted', Response::HTTP_BAD_REQUEST);
        }

        $fromWallet = $this->walletRepository->alreadyHasWallet($data->user_id);

        if (!$this->hasEnoughtBalance($fromWallet, $data->amount)) {
            throw new CustomException('Not enough balance', Response::HTTP_BAD_REQUEST);
        }

        $fromWallet = $this->walletRepository->updateBalance($fromWallet, $data->amount * -1);
        $transactionDto = CreateTransactionDTO::createFromArray([
            'user_id_to' => $user_id_to,
            'wallet_id' => $fromWallet->id,
            'user_id' => $fromWallet->user_id,
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
