<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\TransactionRepositoryInterface;
use App\DTO\CreateTransactionDTO;
use App\Models\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function addTransaction(CreateTransactionDTO $createTransactionDTO)
    {
        Transaction::create($createTransactionDTO->toArray())->save();
    }
}