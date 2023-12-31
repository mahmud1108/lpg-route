<?php

namespace Database\Seeders;

use App\Models\ResetPasswordToken;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;

class ResetPasswordTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ResetPasswordToken::create([
            'email' => 'mahmudawaludin17@gmail.com',
            'token' => Random::generate(150, '0-9a-zA-Z-'),
            'user_id' => 1
        ]);
    }
}
