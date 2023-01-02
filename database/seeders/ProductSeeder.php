<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        for($i = 1; $i<=15; $i++){

            $product = new Product();
            $product->name = $faker->name();
            $product->email = $faker->unique()->safeEmail;
            $product->contact = $faker->phoneNumber;
            $product->detail = $faker->text(110);
            $product->save();
        }
    }
}
