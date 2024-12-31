<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Message;
use App\Http\Utils\Status;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $answers = Answer::with('question.assessment')->get();
            
            return $this->successResponse(Status::OK, Message::ALL_RECORDS->set('answers'), compact('answers'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, 'Failed to retrieve answers. Please try again later.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question_id' => 'required|exists:questions,id',
                'answer_text' => 'required|string',
                'is_correct' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(Status::INVALID_REQUEST, 'There was a validation error', $validator->errors()->toArray());
            }

            $answer = Answer::create($request->all());

            return $this->successResponse(Status::OK, 'The Answer Was Added Successfully', compact('answer'));
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, 'Failed to add answer. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $answer = Answer::with('question.assessment')->find($id);

            if (!$answer) {
                return $this->errorResponse(Status::NOT_FOUND, 'The requested answer was not found.');
            }

            return $this->successResponse(Status::OK, "The request answer record", compact('answer'));
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, 'Failed to retrieve answers. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $answer = Answer::find($id);

            if (!$answer) {
                return $this->errorResponse(Status::NOT_FOUND, 'No answer record found');
            }

            $answer->delete();
            return $this->successResponse(Status::OK, 'The requested answer record deleted successfully');
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, 'Failed to retrieve answer. Please try again later.');
        }
    }
}
