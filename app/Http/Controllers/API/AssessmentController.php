<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Message;
use App\Http\Utils\Status;
use App\Http\Utils\Validation;
use App\Models\Assessment;
use App\Models\Course;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssessmentController extends Controller
{
    public function index()
    {
        try {
            $assessments = Assessment::with('course')->get();
            return $this->successResponse(Status::OK, Message::ALL_RECORDS->set('assessments'), compact('assessments'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), Validation::assessment('create'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$course = Course::find($request->course_id)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('course'));
            }


            if (!$course->assessment()->create($request->except('course_id'))) {
                throw new Exception(Message::FAILED_CREATE->set('assessment'));
            }

            return $this->successResponse(Status::OK, Message::CREATED->set('assessment'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function show(string $assessmentId)
    {
        try {
            $assessment = Assessment::with('course')->find($assessmentId);
            if (!$assessment) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('assessment'));
            };

            return $this->successResponse(Status::OK, Message::RQUESTED_RECORD->set('assessment'), compact('assessment'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(Request $request, string $assessmentId)
    {
        $validation = Validator::make($request->all(), Validation::assessment('update'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$assessment = Assessment::find($assessmentId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('assessment'));
            };

            if (!$assessment->update($request->except('_method'))) {
                throw new Exception(Message::FAILED_UPDATE->set('assessment'));
            }

            return $this->successResponse(Status::OK, Message::UPDATED->set('assessment'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy(string $assessmentId)
    {
        try {
            if (!$assessment = Assessment::find($assessmentId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('assessment'));
            };

            if (!$assessment->delete()) {
                throw new Exception(Message::FAILED_DELETED->set('assessment'));
            }

            return $this->successResponse(Status::OK, Message::DELETED->set('assessment'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
