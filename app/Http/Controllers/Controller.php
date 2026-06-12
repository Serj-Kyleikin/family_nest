<?php

namespace App\Http\Controllers;

use Exception;
use OpenApi\Attributes as OA;
use Illuminate\{
    Http\JsonResponse,
    Http\Response as HTTPResponse
};

#[OA\Info(
    title: 'Application API',
    version: '1.0.0'
)]
#[OA\Server(
    url: '/',
    description: 'Local server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
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
