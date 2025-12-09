<?php

namespace App\Contracts\Repositories;

use App\DTO\CreateTransactionDTO;

interface TransactionRepositoryInterface
{
    public function addTransaction(CreateTransactionDTO $createTransactionDTO);
    public function transactions(int $userId): mixed;
}
