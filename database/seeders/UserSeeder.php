<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'user_id' => 1,
            'name' => 'test',
            'photo' => 'test',
            'email' => 'test@gmail.com',
            'password' => Hash::make('test'),
            'phone' => '333333',
            'bio' => 'test',
            'token' => 'user'
        ]);

        User::create([
            'user_id' => 2,
            'name' => 'test',
            'photo' => 'test',
            'email' => 'new@gmail.com',
            'password' => Hash::make('test'),
            'phone' => '2222',
            'bio' => 'test',
            'token' => 'user1'
        ]);

        User::create([
            'user_id' => 3,
            'name' => 'test',
            'photo' => 'test',
            'email' => 'mahmudawaludin17@gmail.com',
            'password' => Hash::make('test'),
            'phone' => '3465334',
            'bio' => 'test',
            'token' => 'user2'
        ]);
    }
}
