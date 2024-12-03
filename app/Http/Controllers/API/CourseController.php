<?php
// app/Http/Controllers/API/CourseController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Status;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with('user')->where('user_id', Auth::id())->get();

        if($courses->isEmpty())
        {
            return response()->json(['message' => 'No Records Found'], Status::NOT_FOUND->value);
        }
        return response()->json(['courses' => $courses], Status::OK->value);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], Status::INVALID_REQUEST->value);
        }

        $course = new Course();
        $course->user_id = Auth::id();
        $course->title = $request->title;
        $course->description = $request->description;

        $image = $request->file('image');
        $destinationPath = public_path('uploads');
        $imageName = time() .'.'. $image->getClientOriginalExtension();
        $image->move($destinationPath, $imageName);

        $course->image = $imageName;
        $course->save();

        return response()->json(['message' => 'Course Created Successfully!', 'data' => $course], Status::OK->value);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = Course::find($id);

        if(!$course)
        {
            return response()->json(['message' => 'Course Not Found'], Status::NOT_FOUND->value);
        }

        if($course->user_id !== Auth::id())
        {
            return response()->json(['message' => 'Forbidden'], Status::FORBIDDEN->value);
        }

        return response()->json(['course' => $course], Status::OK->value);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails())
        {
            return response()->json(['errors' => $validator->messages()], Status::INVALID_REQUEST->value);
        }

        $course = Course::find($id);

        if(!$course)
        {
            return response()->json(['message' => 'Course Not Found'], Status::NOT_FOUND->value);
        }

        if($course->user_id !== Auth::id())
        {
            return response()->json(['message' => 'Forbidden'], Status::FORBIDDEN->value);
        }

        $course->title = $request->title;
        $course->description = $request->description;

        if($request->hasFile('image'))
        {
            $oldImage = public_path('uploads/' . $course->image);
            if(File::exists($oldImage))
            {
                File::delete($oldImage);
            }

            $image = $request->file('image');
            $destinationPath = public_path('uploads');
            $imageName = time() .'.'. $image->getClientOriginalExtension();
            $image->move($destinationPath, $imageName);
            $course->image = $imageName;
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
        if(!$course)
        {
            return response()->json(['message' => 'Course Not Found'], Status::NOT_FOUND->value);
        }

        if($course->user_id !== Auth::id())
        {
            return response()->json(['message' => 'Forbidden'], Status::FORBIDDEN->value);
        }

        $imagePath = public_path('uploads/' . $course->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $course->delete();
        return response()->json(['message' => 'Course Delete Successfully'], Status::OK->value);
    }
}
?>
