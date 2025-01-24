<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utils\Action;
use App\Http\Utils\Message;
use App\Http\Utils\Role;
use App\Http\Utils\Status;
use App\Models\City;
use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $users = User::with('city')->get();
            return $this->successResponse(Status::OK, 'all users data records', compact('users'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function register(Request $request): JsonResponse
    {
        $validation = Validator::make($data = $request->all(), [
            'name' => 'required|regex:/^[a-zA-Z]+[a-zA-Z0-9\s]*$/',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'city_id' => 'required|exists:cities,id',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required',
            'role' => 'required|in:instructor,student',
            'image' => 'required|mimes:jpg,jpeg,png|max:3072',
        ]);

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, Message::VALIDATION_FAILURE, $validation->errors()->toArray());
        }

        try {
            $image = $request->file('image');
            $data['image'] = 'uploads/' . basename($image->move(public_path('uploads'), $image->hashName()));

            if (!User::create($data)) {
                throw new Exception('something went wrong, failed to register the user');
            }

            return $this->successResponse(Status::CREATED, 'user registration was successfull');
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function validateLogin(Request $request): JsonResponse
    {
        $validation = Validator::make($creds = $request->only('email', 'password'), [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, 'there was validation failure', $validation->errors()->toArray());
        }

        try {
            if (!Auth::attempt($creds)) {
                throw new AuthenticationException('invalid login credentials, please try again');
            }

            if (!$token = auth()->user()->createToken('user_auth_token')->plainTextToken) {
                throw new AuthenticationException('failed to generate authentication token');
            }

            return $this->successResponse(Status::OK, 'you have logged in successfully', [
                'user' => Auth::user()->load('city'),
                'auth_token' => $token,
                'token_type' => 'bearer'
            ]);
        } catch (AuthenticationException $e) {
            return $this->errorResponse(Status::UNAUTHORIZED, $e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function invalidateLogin(Request $request): JsonResponse
    {
        try {
            $request->user()->tokens()->delete();

            return $this->successResponse(Status::OK, 'you have logged out successfully');
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(Request $request): JsonResponse
    {
        $validation = Validator::make($data = $request->all(), [
            'name' => 'sometimes|required|regex:/^[a-zA-Z]+[a-zA-Z0-9\s]*$/',
            'image' => 'sometimes|nullable:false|required|mimes:jpg,jpeg,png|max:3072',
        ]);

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, 'there was validation failure', $validation->errors()->toArray());
        }

        try {
            if (!($id = auth()->id()) || !$user = User::find($id)) {
                throw new Exception('there was an internal server error');
            };

            if (($image = $request->file('image'))) {
                if (File::exists(public_path($image_path = $user->image))) {
                    @File::delete($image_path);
                };

                $data['image'] = 'uploads/' . basename($image->move(public_path('uploads'), $image->hashName()));
            }

            if (!$user->update($data)) {
                throw new Exception('something went wrong, failed to update the user data');
            }

            return $this->successResponse(Status::OK, 'user data was updated successfully', compact('user'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function delete(string $userId): JsonResponse
    {
        try {
            if (!$user = User::find($userId)) {
                return $this->errorResponse(Status::NOT_FOUND, 'invalid user ID');
            }

            if ($user->role === Role::ADMIN) {
                return $this->errorResponse(Status::FORBIDDEN, 'you don\'t have enough privileges to perform this operation');
            }

            if ($user->role === Role::INSTRUCTOR && !User::privileges(Action::DELETE_INSTRUCTOR)) {
                return $this->errorResponse(Status::FORBIDDEN, 'you don\'t have enough privileges to perform this operation');
            }

            if ($user->role === Role::STUDENT && !User::privileges(Action::DELETE_STUDENT)) {
                return $this->errorResponse(Status::FORBIDDEN, 'you don\'t have enough privileges to perform this operation');
            }

            if (File::exists($image_path = $user->image)) File::delete($image_path);

            if (!$user->delete()) {
                throw new Exception('something went wrong, failed to delete the user');
            }

            return $this->successResponse(Status::OK, 'user was deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validation->fails()) {
            return $this->errorResponse(Status::INVALID_REQUEST, 'there was validation failure', $validation->errors()->toArray());
        }

        try {
            if (!($id = auth()->id()) || !$user = User::find($id)) {
                throw new Exception('there was an internal server error');
            };

            if (!Hash::check($request->old_password, $user->password)) {
                return $this->errorResponse(Status::UNAUTHORIZED, 'old password is incorrect');
            }

            if (!$user->update($request->only('password'))) {
                throw new Exception('something went wrong, failed to update the password');
            }

            return $this->successResponse(Status::OK, 'password was updated successfully');
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function cities()
    {
        try {
            $cities = City::all();
            return $this->successResponse(Status::OK, 'all users data records', compact('cities'));
        } catch (Exception $e) {
            return $this->errorResponse(Status::INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
