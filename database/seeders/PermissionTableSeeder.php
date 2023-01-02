<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'agency-list',
            'agency-create',
            'agency-edit',
            'agency-delete',
            'company-list',
            'company-create',
            'company-edit',
            'company-delete',
            'worker-list',
            'worker-create',
            'worker-edit',
            'worker-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            
         ];
       
         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
    }
}
