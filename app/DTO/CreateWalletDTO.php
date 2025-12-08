<?php

namespace App\DTO;

class CreateWalletDTO extends DTO
{
    public int $user_id;
    public string $account;
    public float $balance;

    private function __construct(array $data)
    {
        return parent::__construct($data);
    }

    public static function createFromArray(array $data)
    {
        return new self($data);
    }
}