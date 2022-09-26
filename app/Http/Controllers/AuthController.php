<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    //
    public function index(){


        if (!Auth::check()) {
            return view("pages.login");

        }
        else{
            return redirect('solicitudes');
        }
        
        return view("pages.login");
    }

    public function login(Request $request){
        $rules = [
            "email" => "required",
            "password" => "required"
        ];
        
        $validator = Validator::make($request->input(),$rules);
        
        if($validator->fails()){

            $errores = $validator->errors()->getMessages();

            foreach ($errores as $key =>$value) {
                $errores_formated[$key] = $value[0];
            }
            return response()->json(["data" => $errores_formated],422);
        }
         

        try{

            //$credentials = $request->only('email', 'password');
            $name = $request->input("email");
            $password = $request->input("password");

            
            if (Auth::attempt(['name' => $name, 'password' => $password, 'state_user_id' => 1])) {

                $user = User::with("profile")->find(Auth::id());
                session()->put('user',$user );

                $redirect = "/solicitudes";
                return response()->json(["data" => [ "redirect" => $redirect]],200);
            }

            return response()->json(["data" => [ "messageClient" => "El correo o la contraseña son invalidos"]],403);
    
        }catch(Exception $ex){
                return response()->json(["data" => [ "messageClient" => "No es posible conectarse" , "messageServer"  => $ex->getMessage()]],500);

        }
    }

    public function logout(Request $request){
        Auth::logout();
 
        $request->session()->invalidate();
     
        $request->session()->regenerateToken();
     
        return redirect("login");
    }
}
