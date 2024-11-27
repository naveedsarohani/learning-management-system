<?php

namespace App\Http\Utils;

class Validation
{
    public static function assessment(string $requestType): array
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

    public static function submission(string $requestType): array
    {
        return match ($requestType) {
            'create' => [
                'assessment_id' => 'required',
                'student_id' => 'required',
                'score' => 'required|numeric|min:0',
                'retake_count' => 'required|numeric|min:1|max:255',
            ],
            'update' => [
                'assessment_id' => 'sometimes|required',
                'student_id' => 'sometimes|required',
                'score' => 'sometimes|required|numeric|min:0',
                'retake_count' => 'sometimes|required|numeric|min:1|max:255',
            ],
        };
    }

    public static function lesson(string $requestType): array
    {
        return match ($requestType) {
            'create' => [
                'course_id' => 'required',
                'title' => ['required', 'regex:/^[a-zA-Z][a-zA-Z0-9._@%&|-]*/', 'min:5', 'max:255'],
                'content' => 'required|min:5',
            ],
            'update' => [
                'title' => ['sometimes', 'required', 'regex:/^[a-zA-Z][a-zA-Z0-9._@%&|-]*/', 'min:5', 'max:255'],
                'content' => 'sometimes|required|min:5',
            ],
        };
    }
}
