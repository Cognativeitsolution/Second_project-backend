<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        for($i = 1; $i<=10; $i++){
            $user = new User;
            $user->uuid = rand(10000,9999999);
            $user->name = $faker->name();
            $user->email = $faker->email();
            $user->contact = $faker->phoneNumber;
            $user->sin_number = $faker->phoneNumber;
            $user->is_company = 0;
            $user->is_worker = 1;
            $user->parent_id = $faker->numberBetween(4,7);
            $user->password = bcrypt(123456);
            $user->save();
        }
    }
}
