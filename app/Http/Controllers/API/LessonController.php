<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Message;
use App\Http\Utils\Status;
use App\Http\Utils\Validation;
use App\Models\Course;
use App\Models\Lesson;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        try {
            $lessons = Lesson::with('course')->get();

            return $this->successResponse(Status::OK, Message::ALL_RECORDS->set('lessons'), compact('lessons'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), Validation::lesson('create'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$course = Course::find($request->course_id)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('course'));
            }

            $data = $request->except('course_id');
            if ($request->hasFile('content')) {
                $validation = Validator::make($request->all(), [
                    'content' => 'required|mimes:txt,pdf,docx,mp4,mkv,3gp,mpeg',
                ]);

                if ($validation->fails()) {
                    return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
                }

                $content = $request->file('content');
                if (!$content_path = $content->move(public_path('uploads'), $content->hashName())) {
                    throw new Exception('failed to upload content file');
                };

                $data['content'] = 'uploads/' . basename($content_path);
            } else {
                $data['content'] = $request->input('content');
            }

            if (!$course->lesson()->create($data)) {
                throw new Exception(Message::FAILED_CREATE->set('lesson'));
            }

            return $this->successResponse(Status::CREATED, Message::CREATED->set('lesson'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function show(string $lessonId)
    {
        try {
            if (!$lesson = Lesson::with('course')->find($lessonId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('lesson'));
            };

            return $this->successResponse(Status::OK, Message::RQUESTED_RECORD->set('lesson'), compact('lesson'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(Request $request, string $lessonId)
    {
        $validation = Validator::make($data = $request->all(), Validation::lesson('update'));

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            if (!$lesson = Lesson::find($lessonId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('lesson'));
            };

            if ($request->hasFile('content')) {
                $content = $request->file('content');
                $content_path = $content->move(public_path('uploads'), $content->hashName());
                $data['content'] = 'uploads/' . basename($content_path);

                if (File::exists(public_path($lesson->content))) {
                    File::delete($lesson->content);
                }
            }

            if (!$lesson->update($data)) {
                throw new Exception(Message::FAILED_UPDATE->set('lesson'));
            }

            return $this->successResponse(Status::OK, Message::UPDATED->set('lesson'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy(string $lessonId)
    {
        try {
            if (!$lesson = Lesson::find($lessonId)) {
                return $this->errorResponse(Status::NOT_FOUND, Message::INVALID_ID->set('lesson'));
            };

            if (!$lesson->delete()) {
                throw new Exception(Message::FAILED_DELETED->set('lesson'));
            }

            if (File::exists(public_path($lesson->content))) {
                File::delete($lesson->content);
            }

            return $this->successResponse(Status::OK, Message::DELETED->set('lesson'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
