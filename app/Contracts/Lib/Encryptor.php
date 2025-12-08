<?php

namespace App\Contracts\Lib;

interface Encryptor
{
    public function encrypt(string $value): string;
    public function check(string $value, string $hashedValue): bool;
}