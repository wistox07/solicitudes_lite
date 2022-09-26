<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\PriorityRequest;
use App\Models\Profile;
use App\Models\Request;
use App\Models\SatisfactionRequest;
use App\Models\StateFile;
use App\Models\StateRequest;
use App\Models\TypeRequest;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StateUser;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $roleUser = Role::create(["name" => "User"]);
        $roleAgent = Role::create(["name" => "Agent"]);
        $roleAdmin = Role::create(["name" => "Admin"]);
        
        Permission::create(["name" => "solicitudes.index"])->syncRoles([$roleUser,$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.create"])->syncRoles([$roleUser,$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.store"])->syncRoles([$roleUser,$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.show"])->syncRoles([$roleUser,$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.view"])->syncRoles([$roleUser,$roleAgent,$roleAdmin]);

        Permission::create(["name" => "solicitudes.update"])->syncRoles([$roleUser,$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.nullify"])->syncRoles([$roleUser,$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.comment"])->syncRoles([$roleUser,$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.approve"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.reasign"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.reject"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.print"])->syncRoles([$roleUser,$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.stop"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.close"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "solicitudes.restart"])->syncRoles([$roleAgent,$roleAdmin]);


        Permission::create(["name" => "usuarios.index"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "usuarios.create"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "usuarios.store"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "usuarios.show"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "usuarios.update"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "usuarios.deactivate"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "usuarios.reactivate"])->syncRoles([$roleAgent,$roleAdmin]);
        Permission::create(["name" => "usuarios.restore"])->syncRoles([$roleAdmin]);
        Permission::create(["name" => "usuarios.destroy"])->syncRoles([$roleAdmin]);


        TypeRequest::factory()->create(["name" => "Técnico"]);
        TypeRequest::factory()->create(["name" => "Software"]);

        StateRequest::factory()->create(["name" => "Pendiente"]);
        StateRequest::factory()->create(["name" => "En Proceso"]);
        StateRequest::factory()->create(["name" => "Detenida"]);
        StateRequest::factory()->create(["name" => "Rechazada"]);
        StateRequest::factory()->create(["name" => "Anulada"]);
        StateRequest::factory()->create(["name" => "Completa"]);

        StateFile::factory()->create(["name" => "Activo"]);
        StateFile::factory()->create(["name" => "Eliminado"]);

        PriorityRequest::factory()->create(["name" => "Alta","maximun_hours" => "8"]);
        PriorityRequest::factory()->create(["name" => "Media", "maximun_hours" => "24"]);
        PriorityRequest::factory()->create(["name" => "Baja" , "maximun_hours" => "40"]);
        PriorityRequest::factory()->create(["name" => "Baja" , "maximun_hours" => null]);

        SatisfactionRequest::factory()->create(["name" => "Excelente"]);
        SatisfactionRequest::factory()->create(["name" => "Buena"]);
        SatisfactionRequest::factory()->create(["name" => "Regular"]);
        SatisfactionRequest::factory()->create(["name" => "Mala"]);
        SatisfactionRequest::factory()->create(["name" => "Pesima"]);


        StateUser::factory()->create(["name" => "Activo"]);
        StateUser::factory()->create(["name" => "Desactivo"]);
        StateUser::factory()->create(["name" => "Eliminado"]);



        User::factory(20)->create()->each(function ($user){
           $user->profile()->save(Profile::factory()->make());
           $randomRole = $this->randomRole();
           $user->assignRole([$randomRole]);
        });

        

        DB::table('type_user')->insert([
            'user_id' => 1,
            'type_request_id' => 1,
            "isDefault" => 1
        ]);
        
        DB::table('type_user')->insert([
            'user_id' => 2,
            'type_request_id' => 1,
            "isDefault" => 0
        ]);

        
        DB::table('type_user')->insert([
            'user_id' => 1,
            'type_request_id' => 2,
            "isDefault" => 0
        ]);

        
        DB::table('type_user')->insert([
            'user_id' => 2,
            'type_request_id' => 2,
            "isDefault" => 1
        ]);

        Request::factory(200)->create();



        
    }

    public function array($max){
        $values = [];
        for ($i=0; $i < $max ; $i++) { 
            # code...
            $values[] = $i+1;
        }
        return $values;
    }

    public function randomRole(){
        $roles = ["User","Agent","Admin"];
        return  $roles[array_rand($roles)];

    }
}
