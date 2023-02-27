<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Product;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $super_admin = User::create([
            'uuid' => rand(10000,9999999),
            'name' => 'Tahseen Super Admin',
            'email_verified_at' => now(),
            'email' => 'super_admin@admin.com',
            'contact' => '+92300-3111113',
            'sin_number' => '+92300-3111117',
            'password' => bcrypt('123456'),
            'is_super_admin' => 1,
            'is_worker' => 0
        ]);

        $role_superAdmin = Role::create([
            'name' => 'Super Admin',
            'admin_id_for_role' => $super_admin->id,
            'uuid'  => rand(10000,9999999)
        ]);

        $permissions = Permission::pluck('id','id')->all();

        $role_superAdmin->syncPermissions($permissions);

        $super_admin->assignRole([$role_superAdmin->id]);

        /*** Agency Admin 1 Area ***/

        $agency_admin = User::create([
            'uuid' => rand(10000,9999999),
            'name' => 'Folio 3 LLC',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'contact' => '+92300-3111115',
            'sin_number' => '+92300-3111997',
            'password' => bcrypt('123456'),
            'is_agency' => 1,
            'is_worker' => 0
        ]);

        $role = Role::create([
            'name' => 'Agency Admin',
            'admin_id_for_role' => $agency_admin->id,
            'uuid'  => rand(10000,9999999)
        ]);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $agency_admin->assignRole([$role->id]);


        /*** Agency Admin 2 Area ***/

        $agency_admin2 = User::create([
            'uuid' => rand(10000,9999999),
            'name' => 'Systems LTD',
            'email' => 'admin2@admin.com',
            'email_verified_at' => now(),
            'contact' => '+92300-3111119',
            'sin_number' => '+92300-3111333',
            'password' => bcrypt('123456'),
            'is_agency' => 1,
            'is_worker' => 0
        ]);

        $role = Role::create([
            'name' => 'Agency Admin Second',
            'admin_id_for_role' => $agency_admin2->id,
            'uuid'  => rand(10000,9999999)
        ]);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $agency_admin2->assignRole([$role->id]);
        
    }
}
