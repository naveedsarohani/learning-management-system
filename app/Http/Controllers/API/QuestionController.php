<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Message;
use App\Http\Utils\Status;
use App\Http\Utils\Validation;
use App\Models\Submission;
use App\Models\User;
use App\Models\Question;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;

class QuestionController extends Controller
{
    public function index()
    {
        try {
            $questions = Question::with('assessment')->get();

            if ($questions->isEmpty()) {
                return $this->errorResponse(Status::NOT_FOUND, 'No question records found');
            }

            return $this->successResponse(Status::OK, 'The requested question records', compact('questions'));
        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, 'Failed to retrieve questions. Please try again later.');
        }
    }

   /**
 * Store a newly created question
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\JsonResponse
 */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'assessment_id' => 'required|exists:assessments,id',
                'question_text' => 'required|string',
                'type' => 'required|in:MCQ,true/false',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(Status::INVALID_REQUEST, 'There was a validation error', $validator->errors()->toArray());
            }

            $question = new Question();
            $question->assessment_id = $request->assessment_id;
            $question->question_text = $request->question_text;
            $question->type = $request->type;
            $question->save();

            return $this->successResponse(Status::OK, 'The question was added successfully', compact('question'));
        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, 'Failed to add question. Please try again later.');
        }
    }

    public function show(string $id)
    {
        try{
        $question = Question::find($id);

        if(!$question)
        {
            return $this->errorResponse(Status::NOT_FOUND, 'The requested question was not found.');
        }

        return $this->successResponse(Status::OK, 'The requested enrollment record.', compact('question'));
        } catch(\Exception $e){

            Log::error($e->getMessage());
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, 'Failed to retrieve questions. Please try again later.');
        }
    }

    /**
 * Delete a question by ID
 *
 * @param  string  $id
 * @return \Illuminate\Http\JsonResponse
 */
    public function destroy(string $id)
    {
        try {

            $question = Question::find($id);

            if (!$question) {
                return $this->errorResponse(Status::NOT_FOUND, 'No question record found');
            }

            $question->delete();

            return $this->successResponse(Status::OK, 'The requested record deleted successfully');
        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, 'Failed to delete question. Please try again later.');
        }
    }
}