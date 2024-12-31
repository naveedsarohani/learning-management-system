<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Message;
use App\Http\Utils\Status;
use App\Http\Utils\Validation;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamSubmissionController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $examSubmissions = ExamSubmission::with('exam.instructor', 'student.city')->get();
            return $this->successResponse(Status::OK, Message::ALL_RECORDS->set('exam submissions'), compact('examSubmissions'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), Validation::examSubmission('create'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$exam = Exam::find($request->exam_id)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('exam'));
            }

            if (!$student = User::find($request->student_id)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('student'));
            }

            if (!$examSubmission = ExamSubmission::create($request->all())) {
                throw new Exception(Message::FAILED_CREATE->set('exam submission'));
            }

            return $this->successResponse(Status::CREATED, Message::CREATED->set('exam submission'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function show(string $examSubmissionId): JsonResponse
    {
        try {
            if (!$examSubmission = ExamSubmission::with('exam.instructor', 'student.city')->find($examSubmissionId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('exam submission'));
            };

            return $this->successResponse(Status::OK, Message::RQUESTED_RECORD->set('exam submission'), compact('examSubmission'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(Request $request, ExamSubmission $examSubmission): JsonResponse
    {
        $validation = Validator::make($request->all(), Validation::examSubmission('update'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$examSubmission->update($request->except('_method'))) {
                throw new Exception(Message::FAILED_UPDATE->set('exam submission'));
            }

            return $this->successResponse(Status::OK, Message::UPDATED->set('exam submission'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy(string $examSubmissionId): JsonResponse
    {
        try {
            if (! $examSubmission = ExamSubmission::find($examSubmissionId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('exam submission'));
            }

            if (!$examSubmission->delete()) {
                throw new Exception(Message::FAILED_DELETED->set('exam submission'));
            }

            return $this->successResponse(Status::OK, Message::DELETED->set('exam submission'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
