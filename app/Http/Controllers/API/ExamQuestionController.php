<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Message;
use App\Http\Utils\Status;
use App\Http\Utils\Validation;
use App\Models\Exam;
use App\Models\ExamQuestion;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamQuestionController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $examQuestions = ExamQuestion::with('exam.instructor')->get();
            return $this->successResponse(Status::OK, Message::ALL_RECORDS->set('exam questions'), compact('examQuestions'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), Validation::examQuestion('create'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$exam = Exam::find($request->exam_id)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('exam'));
            }

            if (!$examQuestion = $exam->questions()->create($request->except('exam_id'))) {
                throw new Exception(Message::FAILED_CREATE->set('exam question'));
            }

            return $this->successResponse(Status::CREATED, Message::CREATED->set('exam question'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function show(string $examQuestionId): JsonResponse
    {
        try {
            if (!$examQuestion = ExamQuestion::with('exam.instructor')->find($examQuestionId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('exam question'));
            };
            return $this->successResponse(Status::OK, Message::RQUESTED_RECORD->set('exam question'), compact('examQuestion'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(Request $request, ExamQuestion $examQuestion): JsonResponse
    {
        $validation = Validator::make($request->all(), Validation::examQuestion('update'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$examQuestion->update($request->except('_method'))) {
                throw new Exception(Message::FAILED_UPDATE->set('exam question'));
            }

            return $this->successResponse(Status::OK, Message::UPDATED->set('exam question'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy(string $examQuestionId): JsonResponse
    {
        try {
            if (! $examQuestion = ExamQuestion::find($examQuestionId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('exam question'));
            }

            if (!$examQuestion->delete()) {
                throw new Exception(Message::FAILED_DELETED->set('exam question'));
            }

            return $this->successResponse(Status::OK, Message::DELETED->set('exam question'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
