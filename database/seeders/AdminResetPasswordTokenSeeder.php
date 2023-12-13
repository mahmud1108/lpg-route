<?php

namespace Database\Seeders;

use App\Models\AdminResetPasswordToken;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Nette\Utils\Random;

class AdminResetPasswordTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminResetPasswordToken::create([
            'email' => 'mahmudawaludin17@gmail.com',
            'token' => Random::generate(150, '0-9a-zA-Z-'),
            'admin_id' => 22
        ]);
    }
}
