<?php

namespace App\Enums;

enum TransactionTypeEnum
{
    public const WITHDRAW = 'withdraw';
    public const CREDIT = 'credit';
    public const DEBIT = 'debit';
}
