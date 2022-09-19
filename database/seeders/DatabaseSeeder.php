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


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        TypeRequest::factory()->create(["name" => "Técnico"]);
        TypeRequest::factory()->create(["name" => "Software"]);

        StateRequest::factory()->create(["name" => "Pendiente"]);
        StateRequest::factory()->create(["name" => "En Proceso"]);
        StateRequest::factory()->create(["name" => "Detenido"]);
        StateRequest::factory()->create(["name" => "Rechazado"]);
        StateRequest::factory()->create(["name" => "Eliminado"]);
        StateRequest::factory()->create(["name" => "Completo"]);

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



        User::factory(5)->create()->each(function ($user){
           $user->profile()->save(Profile::factory()->make());
           //$user->types()->attach($this->array(rand(1,2)));
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

        Request::factory(20)->create();

        
    }

    public function array($max){
        $values = [];
        for ($i=0; $i < $max ; $i++) { 
            # code...
            $values[] = $i+1;
        }
        return $values;
    }
}
