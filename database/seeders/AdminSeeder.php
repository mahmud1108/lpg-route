<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'admin_id' => 1,
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
            'phone' => '112111',
            'photo' => 'photo.jpg',
            'token' => 'admin'
        ]);

        Admin::create([
            'admin_id' => 22,
            'name' => 'admindd',
            'email' => 'mahmudawaludin17@gmail.com',
            'password' => Hash::make('admin'),
            'phone' => '11211111',
            'photo' => 'photo.jpg',
            'token' => 'admin1'
        ]);
    }
}
