<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Message;
use App\Http\Utils\Status;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with('user')->get();
        return response()->json(['course', $courses], Status::OK->value);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validator->errors()->toArray());
        }

        $course = new Course();
        $course->user_id = $request->user_id;
        $course->title = $request->title;
        $course->description = $request->description;
        $image = $request->file('image');
        $course->image = 'uploads/' . basename($image->move(public_path('uploads'), $image->hashName()));
        $course->save();

        return response()->json(['message' => 'Course Created Successfully!', 'data' => $course], Status::OK->value);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = Course::with('user')->find($id);

        if (!$course) {
            return response()->json(['message' => 'Course Not Found'], Status::NOT_FOUND->value);
        }

        return response()->json(['course' => $course], Status::OK->value);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:users,id',
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], Status::INVALID_REQUEST->value);
        }

        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Course Not Found'], Status::NOT_FOUND->value);
        }

        $course->user_id = $request->user_id;
        $course->title = $request->title;
        $course->description = $request->description;

        if ($request->hasFile('image')) {
            $oldImage = public_path('uploads/' . $course->image);
            if (File::exists($oldImage)) {
                File::delete($oldImage);
            }

            $image = $request->file('image');
            $course->image = 'uploads/' . basename($image->move(public_path('uploads'), $image->hashName()));
        }
        $course->save();

        return response()->json(['message' => 'Course Details Update Successfully'], Status::OK->value);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json(['message' => 'Course Not Found'], Status::NOT_FOUND->value);
        }

        $imagePath = public_path('uploads/' . $course->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $course->delete();
        return response()->json(['message' => 'Course Delete Successfully'], Status::OK->value);
    }
}
