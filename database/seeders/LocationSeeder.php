<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::create([
            'location_id' => 'asd',
            'address' => 'alamat',
            'holiday' => 'holiday',
            'open_hours' => '11:40:12',
            'inventory' => 'inventory',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'photo' => 'a,jpg',
            'admin_id' => 1,
        ]);
    }
}
