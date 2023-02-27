<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities_file = File::get(base_path('public/cities.json'));

        if ($cities_file) {
            $cities_array = json_decode($cities_file, true);
            foreach ($cities_array as $city) {
                City::insert($city);
            }
            // City::insert($cities_array);
        }
    }
}
