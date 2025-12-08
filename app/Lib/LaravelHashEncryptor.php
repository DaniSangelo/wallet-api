<?php

namespace App\Lib;

use App\Contracts\Lib\Encryptor;
use Illuminate\Support\Facades\Hash;

class LaravelHashEncryptor implements Encryptor
{
    public function encrypt(string $value): string
    {
        return Hash::make($value);
    }

    public function check(string $value, string $hashedValue): bool
    {
        return Hash::check($value, $hashedValue);
    }
}