<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries_file = File::get(base_path('public/countries.json'));

        if ($countries_file) {
            $countries_array = json_decode($countries_file, true);
            Country::insert($countries_array);
        }        
    }
}
