<?php

namespace App\Http\Controllers;

use App\DTO\CreateWalletDTO;
use App\DTO\TransferAmountDTO;
use App\Exceptions\CustomException;
use App\Http\Requests\CreateWalletRequest;
use App\Http\Requests\TransferAmountRequest;
use App\Http\Requests\UpdateWalletBalanceRequest;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends BaseController
{
    public WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;        
    }

    public function create(CreateWalletRequest $request)
    {
        $userId = $request->input('user_id');
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
        try {
            $userId = $request->user()->id;

            if (!$wallet = $this->walletService->getBalance($userId)) {
                throw new CustomException('Wallet not found', Response::HTTP_NOT_FOUND);
            };

            return $this->success('Wallet balance retrieved successfully', [
                'success' => true,
                'message' => 'Wallet balance retrieved successfully',
                'data' => [
                    'balance' => $wallet->balance,
                ],
            ]);
        } catch (CustomException $e) {
            return $this->error('Wallet not found', [
                'success' => false,
                'message' => 'Wallet not found',
                'data' => null,
            ], $e->getCode());
        } catch (Exception $e) {
            return $this->error('Some error occurred on retrieve wallet balance', [
                'success' => false,
                'message' => 'Some error occurred on retrieve wallet balance',
                'data' => null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addBalance(UpdateWalletBalanceRequest $request)
    {
        $userId = $request->user()->id;
        $wallet = $this->walletService->addBalance($userId, $request->amount);
        return $this->success('Wallet balance retrieved successfully', [
            'success' => true,
            'message' => 'Wallet balance retrieved successfully',
            'data' => [
                'balance' => $wallet->balance,
            ],
        ]);
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $amount = $request->input('amount');
        $userId = $request->user()->id;
        $wallet = $this->walletService->withdraw($userId, $amount);
        return $this->success('Withdraw successful', [
            'success' => true,
            'message' => 'Withdraw successful',
            'data' => [
                'balance' => $wallet->balance,
            ],
        ]);
    }

    public function transfer(TransferAmountRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $this->walletService->transfer(TransferAmountDTO::createFromArray($data));
        return $this->success('Transfer successful', [
            'success' => true,
            'message' => 'Transfer successful',
            'data' => null,
        ]);
    }
}
