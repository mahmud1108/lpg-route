<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function testLoginSucess()
    {
        $this->seed(AdminSeeder::class);

        $this->post('/api/admin/login', [
            'username' => 'admin',
            'password' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'admin_id' => 1,
                    'username' => 'admin',
                    'email' => 'admin@gmail.com',
                    'phone' => '112111',
                    'photo' => 'photo.jpg'
                ]
            ]);
    }

    public function testLoginFailed()
    {
        $this->seed(AdminSeeder::class);

        $this->post('/api/admin/login', [
            'username' => 'admin',
            'password' => 'admin123'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Username or Password wrong'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed(AdminSeeder::class);

        $old = Admin::query()->limit(1)->first();
        $this->put('/api/admin/update', [
            'username' => 'adammm',
            'email' => 'emailbaru@gmail.com',
            'photo' => UploadedFile::fake()->create('asdf.jpg', 123),
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->json();
        $new = Admin::query()->limit(1)->first();

        self::assertNotEquals($new->email, $old->email);
        self::assertNotEquals($new->username, $old->username);
        self::assertNotEquals($new->photo, $old->photo);
    }

    public function testUpdateFailed()
    {
        $this->seed(AdminSeeder::class);

        $this->put('/api/admin/update', [
            'username' => 'ada',
            'email' => 'emailbaru',
            'photo' => UploadedFile::fake()->create('asdf.jpg', 123123),
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email field must be a valid email address.'
                    ],
                    'username' => [
                        'The username field must be at least 5 characters.'
                    ],
                    'photo' => [
                        'The photo field must not be greater than 2048 kilobytes.'
                    ],
                ]
            ]);
    }

    public function testLogout()
    {
        $this->seed(AdminSeeder::class);

        $this->delete('/api/admin/logout', headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200);
    }
}
