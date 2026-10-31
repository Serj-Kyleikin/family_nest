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
        $code           = $exception->getCode();
        $isHttpStatus   = is_int($code)
            && $code >= HTTPResponse::HTTP_CONTINUE
            && $code <= HTTPResponse::HTTP_NETWORK_AUTHENTICATION_REQUIRED;
        $status         = $isHttpStatus ? $code : HTTPResponse::HTTP_BAD_REQUEST;

        return response()->json([
            'status'  => false, 
            'message' => $exception->getMessage()
        ], $status);
    }
}
