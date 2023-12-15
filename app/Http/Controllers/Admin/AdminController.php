<?php

namespace App\Http\Controllers\Admin;

use App\Helper\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\AdminRegisterRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use App\Http\Requests\Admin\StoreLocationRequest;
use App\Http\Requests\User\UserPasswordResetRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\LocationResource;
use App\Mail\AdminSendMailResetPassword;
use App\Mail\SendMailResetPassword;
use App\Models\Admin;
use App\Models\AdminResetPasswordToken;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Nette\Utils\Random;

class AdminController extends Controller
{
    public function current_admin()
    {
        $admin = Admin::where('admin_id', auth()->user()->admin_id)->first();

        return new AdminResource($admin);
    }

    public function register(AdminRegisterRequest $request)
    {
        $data = $request->validated();

        $admin = new Admin;
        $admin->admin_id = 'Adm_' . Random::generate(10, '0-9a-z');
        $admin->name = $data['name'];
        $admin->password = Hash::make($data['password']);
        $admin->email = $data['email'];
        $admin->phone = $data['phone'];
        $admin->save();

        return new AdminResource($admin);
    }

    public function login(AdminLoginRequest $request)
    {
        $data = $request->validated();

        $admin = Admin::where('email', $data['email'])->first();

        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Email or Password wrong'
                    ]
                ]
            ], 401));
        }

        $admin->token = Str::uuid()->toString();
        $admin->update();

        return new AdminResource($admin);
    }

    public function update(AdminUpdateRequest $request)
    {
        $data = $request->validated();
        $admin = Admin::where('admin_id', auth()->user()->admin_id)->first();

        if (isset($data['name'])) {
            $admin->name = $data['name'];
        }

        if (isset($data['phone'])) {
            $admin->phone = $data['phone'];
        }

        if (isset($data['email'])) {
            $admin->email = $data['email'];
        }

        if (isset($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        if (isset($data['photo'])) {
            if ($admin->photo) {
                FileHelper::instance()->delete($admin->photo);
                $photo = FileHelper::instance()->upload($data['photo'], 'admin');
            } else {
                $photo = FileHelper::instance()->upload($data['photo'], 'admin');
            }
            $admin->photo = $photo;
        }

        $admin->update();

        return new AdminResource($admin);
    }

    public function logout()
    {
        $admin = Admin::where('admin_id', auth()->user()->admin_id)->first();

        $admin->token = null;
        $admin->save();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Not found.'
                    ]
                ]
            ], 404));
        }

        $cek_email = AdminResetPasswordToken::where('email', $request->email)->count();
        $random = Random::generate(150, '0-9a-zA-Z');
        if ($cek_email > 0) {
            $token = AdminResetPasswordToken::where('email', $request->email)->first();
            $token->token = $random;
            $token->admin_id = $admin->admin_id;
            $token->update();
        } else {
            $token = new AdminResetPasswordToken;
            $token->email = $admin->email;
            $token->token = $random;
            $token->admin_id = $admin->admin_id;
            $token->save();
        }

        $token = AdminResetPasswordToken::where('email', $admin->email)->first();

        $email = new AdminSendMailResetPassword($admin, $token);
        Mail::to($admin->email)->send($email);
    }

    public function reset_action($token, UserPasswordResetRequest $request)
    {
        $cek_token = AdminResetPasswordToken::where('token', $token)->first();
        if (!$cek_token) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Not found.'
                    ]
                ]
            ], 404));
        }

        $admin = Admin::where('email', $cek_token->email)->first();
        if (!$admin) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Not found.'
                    ]
                ]
            ], 404));
        }

        $data = $request->validated();

        $admin->password = Hash::make($data['password']);
        $admin->update();

        return new AdminResource($admin);
    }
}
