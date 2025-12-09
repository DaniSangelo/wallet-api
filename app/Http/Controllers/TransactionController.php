<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends BaseController
{
    public TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function transactions(Request $request)
    {
        $userId = $request->user()->id;
        $transactions = $this->transactionService->transactions($userId);
        return $this->success('Transactions retrieved', [
            'success' => true,
            'message' => 'Transactions retrieved',
            'data' => [
                'items' => $transactions['data'],
                '_pagination' => $transactions['pagination']
            ],
        ]);
    }
}
