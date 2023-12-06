<?php

namespace App\Http\Controllers\User;

use App\Helper\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Nette\Utils\Random;

class UserController extends Controller
{
    public function login(UserRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'email or password wrong'
                    ]
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated();

        $user = new User;
        $user->user_id = 'U_' . Random::generate(10, '0-9');
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function update(UserUpdateRequest $request)
    {
        $data = $request->validated();
        $user = User::where('user_id', auth()->user()->user_id)->first();
        if (isset($data['name'])) {
            $user->name  = $data['name'];
        }

        if (isset($data['email'])) {
            $user->email  = $data['email'];
        }

        if (isset($data['phone'])) {
            $user->phone  = $data['phone'];
        }

        if (isset($data['bio'])) {
            $user->bio  = $data['bio'];
        }

        if (isset($data['photo'])) {
            if ($user->photo == null) {
                $user->photo = FileHelper::instance()->upload($data['photo'], 'user');
            } else {
                FileHelper::instance()->delete($user->photo);
                $user->photo = FileHelper::instance()->upload($data['photo'], 'user');
            }
        }

        if (isset($data['password'])) {
            $user->password  = Hash::make($data['password']);
        }

        $user->update();

        return new UserResource($user);
    }

    public function logout()
    {
        $user = User::where('user_id', auth()->user()->user_id)->first();

        $user->token = null;
        $user->save();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
