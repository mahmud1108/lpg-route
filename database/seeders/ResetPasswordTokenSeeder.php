<?php

namespace Database\Seeders;

use App\Models\ResetPasswordToken;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResetPasswordTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ResetPasswordToken::create([
            'email' => 'mahmudawaludin17@gmail.com',
            'token' => Hash::make('token'),
            'user_id' => 1
        ]);
    }
}
