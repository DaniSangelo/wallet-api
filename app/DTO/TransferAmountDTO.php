<?php

namespace App\DTO;

class TransferAmountDTO extends DTO
{
    public string $to_email;
    public int $user_id;
    public float $amount;

    private function __construct(array $data)
    {
        return parent::__construct($data);
    }

    public static function createFromArray(array $data)
    {
        return new self($data);
    }
}