<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Message;
use App\Http\Utils\Status;
use App\Http\Utils\Validation;
use App\Models\Submission;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $submissions = Submission::with('student')->with('assessment')->get();
            return $this->successResponse(Status::OK, Message::ALL_RECORDS->set('submissions'), compact('submissions'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), Validation::submission('create'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$student = User::find($request->student_id)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('student'));
            }
            if (!User::find($request->assessment_id)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('submission'));
            }

            if (!$student->submission()->create($request->except(['course_id', 'student_id']))) {
                throw new Exception(Message::FAILED_CREATE->set('submission'));
            }

            return $this->successResponse(Status::OK, Message::CREATED->set('submission'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function show(string $submissionId): JsonResponse
    {
        try {
            if (!$submission = Submission::with('student')->with('assessment')->find($submissionId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('submission'));
            };

            return $this->successResponse(Status::OK, Message::RQUESTED_RECORD->set('submission'), compact('submission'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(Request $request, string $submissionId): JsonResponse
    {
        $validation = Validator::make($request->all(), Validation::submission('update'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$submission = submission::find($submissionId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('submission'));
            };

            if (!$submission->update($request->except('_method'))) {
                throw new Exception(Message::FAILED_UPDATE->set('submission'));
            }

            return $this->successResponse(Status::OK, Message::UPDATED->set('submission'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy(string $submissionId): JsonResponse
    {
        try {
            if (!$submission = submission::find($submissionId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('submission'));
            };

            if (!$submission->delete()) {
                throw new Exception(Message::FAILED_DELETED->set('submission'));
            }

            return $this->successResponse(Status::OK, Message::DELETED->set('submission'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
