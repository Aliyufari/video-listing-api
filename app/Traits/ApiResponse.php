<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    protected function response(
        bool $status,
        string $message,
        mixed $data = null,
        string $resourceKey = 'data',
        int $statusCode = Response::HTTP_OK
    ) {
        $response = [
            'status' => $status,
            'message' => $message
        ];

        if (!is_null($data)) {
            $response[$resourceKey] = $data;
        }

        return response()->json($response, $statusCode);
    }
}
