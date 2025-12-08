<?php

namespace App\DTO;

class UserCredentialsDTO extends DTO
{
    public string $email;
    public string $password;

    private function __construct(array $data)
    {
        return parent::__construct($data);
    }

    public static function createFromArray(array $data)
    {
        return new self($data);
    }
}