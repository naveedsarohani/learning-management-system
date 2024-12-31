<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Message;
use App\Http\Utils\Status;
use App\Http\Utils\Validation;
use App\Models\Exam;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $exams = Exam::with('instructor')->get();
            return $this->successResponse(Status::OK, Message::ALL_RECORDS->set('exams'), compact('exams'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($data = $request->all(), Validation::exam('create'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (! Exam::create(['instructor_id' => auth()->id(), ...$data])) {
                throw new Exception(Message::FAILED_CREATE->set('exam'));
            }

            return $this->successResponse(Status::CREATED, Message::CREATED->set('exam'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function show(string $examId): JsonResponse
    {
        try {
            if (!$exam = Exam::with('instructor')->find($examId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('exam'));
            };

            return $this->successResponse(Status::OK, Message::RQUESTED_RECORD->set('exam'), compact('exam'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(Request $request, string $examId): JsonResponse
    {
        $validation = Validator::make($request->all(), Validation::exam('update'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$exam = Exam::find($examId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('exam'));
            };

            if (!$exam->update($request->except('_method'))) {
                throw new Exception(Message::FAILED_UPDATE->set('exam'));
            }

            return $this->successResponse(Status::OK, Message::UPDATED->set('exam'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy(string $examId): JsonResponse
    {
        try {
            if (!$exam = Exam::find($examId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('exam'));
            };

            if (!$exam->delete()) {
                throw new Exception(Message::FAILED_DELETED->set('exam'));
            }

            return $this->successResponse(Status::OK, Message::DELETED->set('exam'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
