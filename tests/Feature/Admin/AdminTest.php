<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\AdminResetPasswordToken;
use Database\Seeders\AdminResetPasswordTokenSeeder;
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

    public function testAdminRegisterSuccess()
    {
        $this->post('/api/admin/register', [
            'name' => 'adminad',
            'password' => 'admin',
            'password_confirmation' => 'admin',
            'email' => 'email@gmail.com',
            'phone' => '123498345'
        ])->assertStatus(201);

        $cek = Admin::where('email', 'email@gmail.com')->get();
        self::assertCount(1, $cek);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/admin/register', [
            'name' => 'adminad',
            'password' => 'admin',
            'password_confirmation' => 'ssss',
            'email' => 'email@gmail.com',
            'phone' => '123498345'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field confirmation does not match.'
                    ]
                ]
            ]);
    }

    public function testLoginSucess()
    {
        $this->seed(AdminSeeder::class);

        $this->post('/api/admin/login', [
            'email' => 'admin@gmail.com',
            'password' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'admin_id' => 1,
                    'name' => 'admin',
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
            'email' => 'admin@gmail.com',
            'password' => 'admin123'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Email or Password wrong'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed(AdminSeeder::class);

        $old = Admin::query()->limit(1)->first();
        $this->put('/api/admin/update', [
            'name' => 'adammm',
            'email' => 'emailbaru@gmail.com',
            'photo' => UploadedFile::fake()->create('asdf.jpg', 123),
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->json();
        $new = Admin::query()->limit(1)->first();

        self::assertNotEquals($new->email, $old->email);
        self::assertNotEquals($new->name, $old->name);
        self::assertNotEquals($new->photo, $old->photo);
    }

    public function testUpdateFailed()
    {
        $this->seed(AdminSeeder::class);

        $this->put('/api/admin/update', [
            'name' => 'ada',
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
                    'name' => [
                        'The name field must be at least 5 characters.'
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

    public function testForgetPasswordForm()
    {
        $this->seed(AdminSeeder::class);

        $this->post('/api/admin/reset-password', [
            'email' => 'mahmudawaludin17@gmail.com'
        ])->assertStatus(200);
    }

    public function testResetPassword()
    {
        $this->seed([AdminSeeder::class, AdminResetPasswordTokenSeeder::class]);
        $token = AdminResetPasswordToken::where('email', 'mahmudawaludin17@gmail.com')->first();
        $old = Admin::where('email', 'mahmudawaludin17@gmail.com')->first();

        $this->post('api/admin/reset-password/' . $token->token, [
            'password' => 'password baru',
            'password_confirmation' => 'password baru'
        ])->assertStatus(200);

        $new = Admin::where('email', 'mahmudawaludin17@gmail.com')->first();
        self::assertNotEquals($new->password, $old->password);
    }

    public function testGetCurrentAdmin()
    {
        $this->seed(AdminSeeder::class);

        $this->get('/api/admin/current', [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    "admin_id" => "1",
                    "name" => "admin",
                    "email" => "admin@gmail.com",
                    "phone" => "112111",
                    "photo" => "photo.jpg"
                ]
            ]);
    }
}
