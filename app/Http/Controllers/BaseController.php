<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    public function success(string $message, array $context = [], int $statusCode = Response::HTTP_OK)
    {
        Log::info($message, $context);

        if (empty($context)) {
            $statusCode = Response::HTTP_NO_CONTENT;
        }

        return response()->json($context, $statusCode);
    }

    public function error(string $message, array $context = [], int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        Log::error($message, $context);
        return response()->json($context, $statusCode);
    }
}
