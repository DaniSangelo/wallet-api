<?php

namespace App\Http\Controllers;

use App\DTO\CreateWalletDTO;
use App\Http\Requests\CreateWalletRequest;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends BaseController
{
    public WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;        
    }

    public function create(CreateWalletRequest $request)
    {
        $userId = 1; //todo: get from auth middleare
        $createWalletDto = CreateWalletDTO::createFromArray([
            'user_id' => $userId,
            'account' => $request->account,
            'balance' => 0,
        ]);
        $this->walletService->create($createWalletDto);
        return $this->success('Wallet successfully created', ['success' => true, 'message' => 'Wallet successfully created', 'data' => null]);
    }

    public function balance(Request $request)
    {
        $userId = 1; //todo: get from auth middleware
        $wallet = $this->walletService->getBalance($userId);
        return $this->success('Wallet balance retrieved successfully', [
            'success' => true,
            'message' => 'Wallet balance retrieved successfully',
            'data' => [
                'balance' => $wallet->balance,
            ],
        ]);
    }
}
