<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SpatieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = Role::create(["name" => "User"]);
        $encargado = Role::create(["name" => "Encargado"]);
        $admin = Role::create(["name" => "Admin"]);
        
        Permission::create(["name" => "users_index"])->syncRoles([$admin,$encargado]);
        Permission::create(["name" => "users_create"])->syncRoles([$admin,$encargado]);
        Permission::create(["name" => "users_store"])->syncRoles([$admin,$encargado]);
        Permission::create(["name" => "users_show"])->syncRoles([$admin,$encargado]);
        Permission::create(["name" => "users_update"])->syncRoles([$admin,$encargado]);
        Permission::create(["name" => "users_destroy"])->syncRoles([$admin]);
        Permission::create(["name" => "users_activate"])->syncRoles([$admin,$encargado]);
        Permission::create(["name" => "users_deactivate"])->syncRoles([$admin,$encargado]);
        Permission::create(["name" => "users_restore"])->syncRoles([$admin]);
    }
}
