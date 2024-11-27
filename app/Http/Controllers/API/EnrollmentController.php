<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Status;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enrollments = Enrollment::with('course', 'student')->get();

        if ($enrollments->isEmpty()) {
            return $this->successResponse(Status::NOT_FOUND, 'No enrollment records found');
        }
        return $this->successResponse(Status::OK, 'the requested course record', compact('enrollments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, 'there was the validations error', $validator->errors()->toArray());
        }

        $enrollment = new Enrollment();
        $enrollment->course_id = $request->course_id;
        $enrollment->user_id = $request->user_id;
        $enrollment->save();

        return $this->successResponse(Status::OK, 'the enrollment was added successfully', compact('enrollment'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return $this->errorResponse(Status::NOT_FOUND, 'The requested enrollment was not found.');
        }

        return $this->successResponse(Status::OK, 'The requested enrollment record.', compact('enrollment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return $this->errorResponse(Status::NOT_FOUND, 'The requested enrollment was not found.');
        }

        $enrollment->delete();
        return $this->successResponse(Status::OK, 'The requested enrollment record delete successfully.', compact('enrollment'));
    }
}
