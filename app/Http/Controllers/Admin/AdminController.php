<?php

namespace App\Http\Controllers\Admin;

use App\Helper\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function login(AdminLoginRequest $request)
    {
        $data = $request->validated();

        $admin = Admin::where('username', $data['username'])->first();

        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Username or Password wrong'
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

        if (isset($data['username'])) {
            $admin->username = $data['username'];
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
}
