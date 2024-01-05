<?php

namespace App\Http\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class GetData extends Controller
{
    public static function data_check($query)
    {
        $data = $query;
        if (!$data) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Not found.'
                    ]
                ]
            ], 404));
        }

        return $data;
    }

    public static function login_validation($query, $password)
    {
        $data = $query;

        if (!$data || !Hash::check($password, $data->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Email or Password wrong'
                    ]
                ]
            ], 401));
        }

        return $data;
    }

    public static function instance()
    {
        return new GetData();
    }
}
