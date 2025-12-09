<?php

namespace App\Services;

use App\Contracts\Repositories\TransactionRepositoryInterface;

class TransactionService
{
    public TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function transactions(int $userId)
    {
        $data = $this->transactionRepository->transactions($userId);
        $items = $data->items();
        return [
            'pagination' => [
                'total' => $data->total(),
                'currentPage' => $data->currentPage(),
                'lastPage' => $data->lastPage(),
                'perPage' => $data->perPage(),
            ],
            'data' => $items,
        ];
    }
}