<?php

namespace App\DTO;

use App\TransactionTypeEnum;

class CreateTransactionDTO extends DTO
{
    public int $user_id_to;
    public int $wallet_id;
    public string $user_id;
    public float $amount;
    public string $type;

    private function __construct(array $data)
    {
        return parent::__construct($data);
    }

    public static function createFromArray(array $data)
    {
        return new self($data);
    }
}