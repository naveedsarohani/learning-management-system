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
                // 'content' => 'required|min:5',
                'content' => 'required||mimes:mp4,mkv,3gp,mpeg',
            ],
            'update' => [
                'title' => ['sometimes', 'required', 'regex:/^[a-zA-Z][a-zA-Z0-9._@%&|-]*/', 'min:5', 'max:255'],
                // 'content' => 'sometimes|required|min:5',
                'content' => 'sometimes|required|mimes:mp4,mkv,3gp,mpeg',
            ],
        };
    }

    public static function exam(string $requestType): array
    {
        return match ($requestType) {
            'create' => [
                'title' => ['required', 'regex:/^[a-zA-Z]+[a-zA-Z0-9\s_\.%@-]{4,255}$/'],
                'description' => 'required',
                'passing_percentage' => 'required|numeric|min:0|max:100',
                'time_allowed' => 'required|numeric|min:1',
                'starts_at' => 'required|date|after:now',
            ],
            'update' => [
                'title' => ['sometimes', 'required', 'regex:/^[a-zA-Z]+[a-zA-Z0-9\s_\.%@-]{4,255}$/'],
                'description' => 'sometimes|required',
                'passing_percentage' => 'sometimes|required|numeric|min:0|max:100',
                'time_allowed' => 'sometimes|required|numeric|min:1',
                'starts_at' => 'sometimes|required|date|after:now',
            ],
        };
    }

    public static function examQuestion(string $requestType): array
    {
        return match ($requestType) {
            'create' => [
                'exam_id' => 'required|exists:exams,id',
                'question_text' => 'required',
                'answers' => 'required',
                'correct_option' => 'required',
                'carry_marks' => 'required|numeric|min:0',
            ],
            'update' => [
                'question_text' => 'sometimes|required',
                'answers' => 'sometimes|required',
                'correct_option' => 'sometimes|required',
                'carry_marks' => 'sometimes|required|numeric|min:0',
            ],
        };
    }

    public static function examSubmission(string $requestType): array
    {
        return match ($requestType) {
            'create' => [
                'exam_id' => 'required|exists:exams,id',
                'student_id' => 'required|exists:users,id',
                'total_questions' => 'required|numeric|min:0',
                'obtained_marks' => 'required|numeric|min:0',
                'total_marks' => 'required|numeric|min:0',
                'total_correct' => 'required|numeric|min:0',
                'total_wrong' => 'required|numeric|min:0',
            ],
            'update' => [
                'total_questions' => 'sometimes|required|numeric|min:0',
                'obtained_marks' => 'sometimes|required|numeric|min:0',
                'total_marks' => 'sometimes|required|numeric|min:0',
                'total_correct' => 'sometimes|required|numeric|min:0',
                'total_wrong' => 'sometimes|required|numeric|min:0',
            ],
        };
    }

    public static function progress(string $requestType): array
    {
        return match ($requestType) {
            'create' => [
                'user_id' => 'required|exists:users,id',
                'course_id' => 'required|exists:courses,id',
                'lesson_index' => 'sometimes|required|numeric|min:0|max:256',
                'progress_status' => 'sometimes|required|in:not_started,in_progress,completed',
                'completion_percentage' => 'sometimes|required|numeric|min:0|max:100',
            ],
            'update' => [
                'lesson_index' => 'sometimes|required|numeric|min:0|max:256',
                'progress_status' => 'sometimes|required|in:not_started,in_progress,completed',
                'completion_percentage' => 'sometimes|required|numeric|min:0|max:100',
            ],
        };
    }
}
