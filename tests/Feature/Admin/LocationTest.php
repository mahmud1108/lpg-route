<?php

namespace Tests\Feature\Admin;

use App\Models\Location;
use Database\Seeders\AdminSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\ManyLocationSeeder;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

class LocationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testStoreLoctionSuccess()
    {
        $this->seed(AdminSeeder::class);
        $this->post('/api/admin/location', [
            'address' => 'mergosri',
            'holiday' => '-',
            'open_hours' => '11:40:12',
            'inventory' => 4,
            'latitude' => 1398587,
            'longitude' => 1398587,
            'photo' => UploadedFile::fake()->create('asddfad.jpg', 100),
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(201);
    }

    public function testStoreLocationFailed()
    {
        $this->seed(AdminSeeder::class);
        $this->post('/api/admin/location', [
            'address' => 'mergosri',
            'holiday' => '-',
            'open_hours' => '11:40:12',
            'inventory' => 4,
            'latitude' => 1398587,
            'longitude' => 1398587,
            'photo' => UploadedFile::fake()->create('asddfad.jpg', 100),
        ], [
            'Authorization' => 'd'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorize'
                    ]
                ]
            ]);
    }

    public function testUpdateLocationSuccess()
    {
        $this->seed([AdminSeeder::class, LocationSeeder::class]);
        $old = Location::query()->limit(1)->first();

        $this->put('/api/admin/location/asd', [
            'address' => 'madukara'
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(200)->json();
        $new = Location::query()->limit(1)->first();

        self::assertNotEquals($new->address, $old->address);
    }

    public function testUpdateFailed()
    {
        $this->seed([AdminSeeder::class, LocationSeeder::class]);

        $this->put('/api/admin/location/asd', [
            'inventory' => 'asdf'
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'inventory' => [
                        'The inventory field must be an integer.'
                    ]
                ]
            ]);
    }

    public function testUpdateNotFound()
    {
        $this->seed([AdminSeeder::class, LocationSeeder::class]);

        $this->put('/api/admin/location/0', [
            'inventory' => '11'
        ], [
            'Authorization' => 'admin'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found.'
                    ]
                ]
            ]);
    }

    public function testGetAllByAdminSuccess()
    {
        $this->seed([AdminSeeder::class, ManyLocationSeeder::class]);

        $result = $this->get('/api/admin/location', [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->json();

        self::assertEquals(20, $result['meta']['total']);
        self::assertEquals(10, $result['meta']['per_page']);
        self::assertEquals(10, count($result['data']));
    }

    public function testGetAllByAdminFailed()
    {
        $this->seed([AdminSeeder::class, LocationSeeder::class]);

        $this->get('/api/admin/location', [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorize'
                    ]
                ]
            ]);
    }

    public function testGetOneSuccess()
    {
        $this->seed([AdminSeeder::class, LocationSeeder::class]);

        $this->get('/api/admin/location/asd', [
            'Authorization' => 'admin'
        ])->assertStatus(200);
    }

    public function testGetOneFailed()
    {
        $this->seed([AdminSeeder::class, LocationSeeder::class]);

        $this->get('/api/admin/location/ioiuo', [
            'Authorization' => 'admin'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found.'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([AdminSeeder::class, LocationSeeder::class]);

        $this->delete('/api/admin/location/asd', headers: [
            'Authorization' => 'admin'
        ])->assertStatus(200)
            ->assertJson([
                'status' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([AdminSeeder::class, LocationSeeder::class]);

        $this->delete('/api/admin/location/2222', headers: [
            'Authorization' => 'admin'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Not found.'
                    ]
                ]
            ]);
    }
}
