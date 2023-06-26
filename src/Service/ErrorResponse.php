<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse extends JsonResponse
{
    public function __construct(string $message = null, int $status = Response::HTTP_OK, array $headers = [], bool $json = false)
    {
        parent::__construct(
            array_merge([
                'status' => 'error'
            ], is_null($message) ? [] : ['result' => $message]),
            $status,
            $headers,
            $json
        );
    }
}