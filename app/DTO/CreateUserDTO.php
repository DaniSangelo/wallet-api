<?php

namespace App\DTO;

class CreateUserDTO extends DTO
{
    public string $name;
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