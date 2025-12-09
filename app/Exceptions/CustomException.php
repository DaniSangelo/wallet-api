<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CustomException extends Exception
{
    public function __construct(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, array $context = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
    }
}
