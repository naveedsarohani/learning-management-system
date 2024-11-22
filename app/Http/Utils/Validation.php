<?php

namespace App\Http\Utils;

class Validation
{
    public static function assessment(string $requestType)
    {
        return match ($requestType) {
            'create' => [
                'title' => ['required', 'regex:/^[a-zA-Z]+[a-zA-Z0-9\s_\.%@-]{4,255}$/'],
                'type' => 'required|in:quiz,test,exam',
                'time_limit' => 'required|numeric|min:1',
                'retakes_allowed' => 'required|numeric|min:1'
            ],
            'update' => [
                'title' => ['sometimes', 'required', 'regex:/^[a-zA-Z]+[a-zA-Z0-9\s_\.%@-]{4,255}$/'],
                'type' => 'sometimes|required|in:quiz,test,exam',
                'time_limit' => 'sometimes|required|numeric|min:1',
                'retakes_allowed' => 'sometimes|required|numeric|min:1'
            ],
        };
    }
}
