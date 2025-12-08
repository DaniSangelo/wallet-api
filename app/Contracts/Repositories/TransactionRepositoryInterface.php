<?php

namespace App\Contracts\Repositories;

use App\DTO\CreateTransactionDTO;
use App\DTO\CreateWalletDTO;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function addTransaction(CreateTransactionDTO $createTransactionDTO);
}
