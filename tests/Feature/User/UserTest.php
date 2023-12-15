<?php

namespace Tests\Feature\User;

use App\Models\ResetPasswordToken;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\ResetPasswordTokenSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testUserLoginSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/user/login', [
            'email' => 'test@gmail.com',
            'password' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'test',
                    'photo' => 'test',
                    'email' => 'test@gmail.com',
                    'phone' => '333333',
                    'bio' => 'test'
                ]
            ]);
    }

    public function testUserLoginFailed()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/user/login', [
            'email' => 'test@gmail.com',
            'password' => 'salah password'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'email or password wrong'
                    ]
                ]
            ]);
    }

    public function testUserRegisterSuccess()
    {
        $this->post('/api/user/register', [
            'name' => 'mahmud',
            'email' => 'mahmud@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '3323233'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'mahmud',
                    'email' => 'mahmud@gmail.com',
                    'phone' => '3323233'
                ]
            ]);
    }

    public function testUserRegisterFailedEmail()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/user/register', [
            'name' => 'mahmud',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '3323233'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email has already been taken.'
                    ]
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed(UserSeeder::class);

        $user = User::query()->limit(1)->first();
        $this->put('/api/user/update', [
            'name' => 'mahmud awaludin',
            'email' => 'emailbaru@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'photo' => UploadedFile::fake()->create('asdfasdf.jpg', 1024),
            'phone' => '122344'
        ], [
            'Authorization' => 'user'
        ])->assertStatus(200)
            ->json();
        $new = User::query()->limit(1)->first();

        self::assertNotEquals($new->name, $user->name);
        self::assertNotEquals($new->password, $user->password);
    }

    public function testUpdateFailedValidation()
    {
        $this->seed(UserSeeder::class);

        $this->put('/api/user/update', [
            'email' => 'new@gmail.com',
            'phone' => '2222',
            'photo' => UploadedFile::fake()->create('ajdfjadfasdf.pdf', 10000)
        ], [
            'Authorization' => 'user'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email has already been taken.'
                    ],
                    'phone' => [
                        'The phone has already been taken.'
                    ],
                    'photo' => [
                        'The photo field must be an image.',
                        'The photo field must be a file of type: jpg, png, jpeg.',
                        'The photo field must not be greater than 2048 kilobytes.'
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->delete('/api/user/logout', headers: [
            'Authorization' => 'user'
        ])->assertStatus(200);
    }

    public function testLogoutFailed()
    {
        $this->seed(UserSeeder::class);

        $this->delete('/api/user/logout', headers: [
            'Authorization' => 'token salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorioze'
                    ]
                ]
            ]);
    }

    public function testSendEmail()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/user/reset-password', [
            'email' => 'mahmudawaludin17@gmail.com'
        ])->assertStatus(200);
    }

    public function testResetPassword()
    {
        $this->seed([UserSeeder::class, ResetPasswordTokenSeeder::class]);
        $token = ResetPasswordToken::where('email', 'mahmudawaludin17@gmail.com')->first();
        $old = User::where('email', 'mahmudawaludin17@gmail.com')->first();

        $this->post('api/user/reset-password/' . $token->token, [
            'password' => 'password baru',
            'password_confirmation' => 'password baru'
        ])->assertStatus(200);

        $new = User::where('email', 'mahmudawaludin17@gmail.com')->first();
        self::assertNotEquals($new->password, $old->password);
    }

    public function testSearchSuccess()
    {
        $this->seed([AdminSeeder::class, LocationSeeder::class, UserSeeder::class]);

        $result = $this->get('/api/user/search/a', [
            'Authorization' => 'user'
        ])->assertStatus(200)->json();

        self::assertEquals(1, count($result['data']));
        self::assertEquals(1, $result['meta']['total']);
    }

    public function testGetCurrentUser()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/user/current', [
            'Authorization' => 'user'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    "user_id" => "1",
                    "name" => "test",
                    "email" => "test@gmail.com",
                    "phone" => "333333",
                    "photo" => "test",
                    "bio" => "test",
                    "token" => "user"
                ]
            ]);
    }
}
