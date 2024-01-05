<?php

namespace App\Http\Controllers\User;

use App\Helper\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GetData;
use App\Http\Requests\User\UserPasswordResetRequest;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Requests\User\UserRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\LocationResource;
use App\Http\Resources\UserResource;
use App\Mail\SendMailResetPassword;
use App\Models\Location;
use App\Models\ResetPasswordToken;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Nette\Utils\Random;

class UserController extends Controller
{
    public function current_user()
    {
        $user = User::where('user_id', auth()->user()->user_id)->first();

        return new UserResource($user);
    }

    public function login(UserRequest $request)
    {
        $data = $request->validated();
        $user = GetData::login_validation(User::where('email', $data['email'])->first(), $data['password']);

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

    public function reset_password(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = GetData::data_check(User::where('email', $request->email)->first());

        $cek_email = ResetPasswordToken::where('email', $request->email)->count();
        $random = Random::generate(150, '0-9a-zA-Z-');
        if ($cek_email > 0) {
            $token = ResetPasswordToken::where('email', $request->email)->first();
            $token->token = $random;
            $token->user_id = $user->user_id;
            $token->update();
        } else {
            $token = new ResetPasswordToken;
            $token->email = $user->email;
            $token->token = $random;
            $token->user_id = $user->user_id;
            $token->save();
        }

        $token = ResetPasswordToken::where('email', $user->email)->first();

        $email = new SendMailResetPassword($user, $token);
        Mail::to($user->email)->send($email);
    }

    public function reset_action($token, UserPasswordResetRequest $request)
    {
        $cek_token = GetData::data_check(ResetPasswordToken::where('token', $token)->first());

        $user = GetData::data_check(User::where('email', $cek_token->email)->first());

        $data = $request->validated();

        $user->password = Hash::make($data['password']);
        $user->update();

        return new UserResource($user);
    }

    public function search($address, Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $location = Location::query()
            ->where('address', 'like', '%' . $address . '%')
            ->paginate(perPage: $per_page, page: $page);

        return LocationResource::collection($location);
    }

    public function get_all(Request $request)
    {
        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $location =  Location::paginate(perPage: $per_page, page: $page);

        return LocationResource::collection($location);
    }
}
