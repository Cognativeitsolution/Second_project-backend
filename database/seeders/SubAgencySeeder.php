<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\SubAgency;

class SubAgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        for($i = 1; $i<=5; $i++){

            $sub_agency = new SubAgency;
            
            $sub_agency->sub_agency_name = $faker->realText(20);
            $sub_agency->markup_rate = $faker->unique()->numberBetween(20,100);
            $sub_agency->agency_id = 2 ;
            
            $sub_agency->save();

        }
    }
}
