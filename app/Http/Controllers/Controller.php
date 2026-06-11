<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\{
    Http\JsonResponse,
    Http\Response as HTTPResponse
};

abstract class Controller
{
    protected function error(Exception $exception): JsonResponse
    {
        $code = strlen($exception->getCode()) === 3 ? $exception->getCode() : HTTPResponse::HTTP_BAD_REQUEST;

        return response()->json([
            'status'  => false, 
            'message' => $exception->getMessage()
        ], $code);
    }
}
