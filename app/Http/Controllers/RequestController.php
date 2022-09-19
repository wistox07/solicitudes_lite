<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Request as ModelsRequest;
use App\Models\StateRequest;
use App\Models\TypeRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DataTables;



use Illuminate\Http\Request;


class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   $data["title_html"] = "Solicitudes";
        $data["title_module"] = "Solicitudes";
        $data["types"] = TypeRequest::select(["id","name"])->where("isActive","=",true)->get();
        $data["states"] = StateRequest::select(["id","name"])->where("isActive","=",true)->get();
        //$data["petitioners"] = User::has('register_requests')->with("profile")->get();

        $data["petitioners"] = User::from('users as u')
        ->select(["u.id as id","p.name as name"])
        ->join("profiles as p","u.id", "=","p.user_id")
        ->join("requests as r","u.id", "=","r.petitioner_id")
        ->whereNotIn('u.state_user_id', [3])
        ->groupBy("id","name")
        ->get();
                    
        $data["agents"] = User::from('users as u')
        ->select(["u.id as id","p.name as name"])
        ->join("profiles as p","u.id", "=","p.user_id")
        ->join("requests as r","u.id", "=","r.agent_id")
        ->whereNotIn('u.state_user_id', [3])
        ->groupBy("id","name")
        ->get();

        return view('pages.request',$data);
    }

    public function list(Request $request){

        
        $rules = [
            "ticket" => "nullable",
            "f_type" => "numeric",
            "f_state" =>"numeric",
            "f_petitioner" =>"numeric",
            "f_agent" =>"numeric",
            //"f_date_init" => "required|date_format:Y-m-d",
            //"f_date_end" => "required|date_format:Y-m-d|after_or_equal:f_date_end"
        ];

        
        $validator = Validator::make($request->input(),$rules);

        if($validator->fails()){

            $errores = $validator->errors()->getMessages();
            foreach ($errores as $key =>$value) {
                $errores_formated[$key] = $value[0];
            }

            return response()->json(["data" => $errores_formated],422);
        }
        
        $requests = ModelsRequest::from("requests as r")
        ->join("users as u_peti", "u_peti.id" ,"=","r.petitioner_id")
        ->join("profiles as p_peti", "p_peti.user_id" ,"=","u_peti.id")
        ->join("users as u_agent", "u_agent.id" ,"=","r.agent_id")
        ->join("profiles as p_agent", "p_agent.user_id" ,"=","u_agent.id")

        ->join("type_requests as type", "type.id" ,"=","r.type_request_id")
        ->leftJoin("priority_requests as prio","prio.id","=", "r.priority_request_id")
        ->join("state_requests as st", "st.id" ,"=","r.state_request_id")
        ->leftJoin("satisfaction_requests as sa","sa.id","=", "r.satisfaction_request_id")

        ->select([
            "r.id", 
            DB::raw("CONCAT(p_peti.name , ' ',p_peti.last_name) as petitioner"),
            "r.description",
            DB::raw("CONCAT(p_agent.name , ' ',p_agent.last_name) as agent"),
            "type.name as type",
            DB::raw("IFNULL(prio.name , 'Sin Asignar') as priority"),
            "prio.name as progress",
            "st.name as state",
            "r.register_date",
            DB::raw("IFNULL(sa.name , 'Sin Asignar') as satisfaction")
            ])
        ->get();

        return Datatables::of($requests)
         //->addColumn('acciones', "usuario_opciones")
         ->addColumn('options', function($row){
            $options = "<div class='dropdown no-arrow'>
            <button type='button' class='btn btn-primary btn-sm dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'><i class='fa fa-cogs'></i></button>
            <div class='dropdown-menu dropdown-menu-right shadow animated--fade-in' aria-labelledby='dropdownMenuLink'>";
            $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" => $row->id])  ."'>Ver</a>";
            return $options;
        })
        
        
        ->rawColumns(['options'])
        ->make(true);
    

        //$requests = ModelsRequest::from("requests as r")
        //->select(["r.start_date"])->get();
        //->get();
        //return $requests;
        

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
