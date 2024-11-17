<?php

namespace App\Http\Utils;

use App\Http\Utils\Status;

trait ApiResponse
{
    public function successResponse(Status $code, string $message, array|string $data = null)
    {
        $responseData = [
            'status' => true,
            'messages' => $message,
            'data' => null
        ];

        if ($data && is_array($data)) {
            foreach ($data as $key => $value) {
                $responseData['data'][$key] = $value;
            };
        }

        return response()->json($responseData, $code->value);
    }

    public function errorResponse(Status $code, string $message, array|string $data = null)
    {
        $responseData = [
            'status' => false,
            'messages' => $message,
        ];

        if ($data && is_array($data)) {
            $responseData['errors'] = array_map(fn($message) => $message[0], $data);
        }

        if ($data && is_string($data)) {
            $responseData['error'] = $data;
        }

        return response()->json($responseData, $code->value);
    }
}
