<?php

namespace App\Traits;

trait ApiResponse
{
    
    protected function successResponse($data, string $message = '', int $status = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function errorResponse(string $message, int $status = 400)
    {
        return response()->json([
            'message' => $message,
            'data' => null
        ], $status);
    }
}
