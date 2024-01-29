<?php

namespace App\Traits;

use \Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

trait HttpResponses
{
    protected function formatErrors(ValidationException $exception): array
    {
        $errors = [];
        foreach ($exception->errors() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message
                ];
            }
        }

        return $errors;
    }

    protected function success($data = [], string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'status_code' => $code,
            'data' => $data,
            'message' => $message
        ], $code);
    }

    protected function error(string $message, int $code, $data = []): JsonResponse|\Throwable
    {

        return response()->json([
            'status' => false,
            'status_code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
