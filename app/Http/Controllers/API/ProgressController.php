<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Message;
use App\Http\Utils\Status;
use App\Http\Utils\Validation;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Progress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        try {
            $progress = Progress::with('user.city', 'course')->where('user_id', $request->user()->id)->get();

            return $this->successResponse(Status::OK, Message::ALL_RECORDS->set('curse progress'), compact('progress'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), Validation::progress('create'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            $data = $request->all();
            $progress = Progress::firstOrNew([
                'user_id' => $request->user()->id,
                'course_id' => $data['course_id'],
            ]);

            if ($lessonIndex = $request->lesson_index) {
                $progress->lesson_index = $lessonIndex;
            }

            if ($progressStatus = $request->progress_status) {
                $progress->progress_status = $progressStatus;
            }

            if ($completionPercentage = $request->completion_percentage) {
                $progress->completion_percentage = $completionPercentage;
            }

            if (!$progress->save()) {
                throw new Exception(Message::FAILED_CREATE->set('course progress'));
            }

            return $this->successResponse(Status::CREATED, Message::CREATED->set('course progress'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function show(Request $request, string $courseId)
    {
        try {
            $progresses = Progress::with('user.city', 'course',)->where('user_id', $request->user()->id)->where('course_id', $courseId)->get();

            return $this->successResponse(Status::OK, Message::RQUESTED_RECORD->set('course progress'), compact('progresses'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(Request $request, string $courseId)
    {
        $validation = Validator::make($request->all(), Validation::progress('update'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            $progress = Progress::where('user_id', $request->user()->id)->where('course_id', $courseId)->first();

            if (!$progress) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('courses progress'));
            }

            if ($lessonIndex = $request->lesson_index) {
                $progress->lesson_index = $lessonIndex;
            }

            if ($progressStatus = $request->progress_status) {
                $progress->progress_status = $progressStatus;
            }

            if ($completionPercentage = $request->completion_percentage) {
                $progress->completion_percentage = $completionPercentage;
            }

            if (!$progress->save()) {
                throw new Exception(Message::FAILED_UPDATE->set('course progress'));
            }

            return $this->successResponse(Status::OK, Message::UPDATED->set('course progress'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy(Request $request, string $courseId)
    {
        try {
            $progress = Progress::where('user_id', $request->user()->id)->where('course_id', $courseId)->first();

            if (!$progress) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('course progress'));
            }

            if (!$progress->delete()) {
                throw new Exception(Message::FAILED_DELETED->set('course progress'));
            }

            return $this->successResponse(Status::OK, Message::DELETED->set('course progress'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
