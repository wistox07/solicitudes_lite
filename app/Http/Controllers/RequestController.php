<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Models\Profile;
use App\Models\Request as ModelsRequest;
use App\Models\StateRequest;
use App\Models\TypeRequest;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Exception;



use Illuminate\Http\Request;


class RequestController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth");
        $this->middleware("can:solicitudes.index")->only("index");
        $this->middleware("can:solicitudes.create")->only("create");
        $this->middleware("can:solicitudes.store")->only("store");
        $this->middleware("can:solicitudes.show")->only("show");
        $this->middleware("can:solicitudes.view")->only("view");


        /*
        $this->middleware("can:solicitudes.update")->only("update");
        $this->middleware("can:solicitudes.nullify")->only("nullify");
        $this->middleware("can:solicitudes.comment")->only("comment");
        $this->middleware("can:solicitudes.approve")->only("approve");
        $this->middleware("can:solicitudes.reasign")->only("reasign");
        $this->middleware("can:solicitudes.reject")->only("reject");
        $this->middleware("can:solicitudes.print")->only("print");
        $this->middleware("can:solicitudes.stop")->only("stop");
        $this->middleware("can:solicitudes.close")->only("close");
        $this->middleware("can:solicitudes.restart")->only("restart");
        */
        
    }
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

        $petitioners = User::from('users as u')
        ->select(["u.id as id","p.name as name"])
        ->join("profiles as p","u.id", "=","p.user_id")
        ->join("requests as r","u.id", "=","r.petitioner_id")
        ->whereNotIn('u.state_user_id', [3]);


        $agents= User::from('users as u')
        ->select(["u.id as id","p.name as name"])
        ->join("profiles as p","u.id", "=","p.user_id")
        ->join("requests as r","u.id", "=","r.agent_id")
        ->whereNotIn('u.state_user_id', [3]);


        if(session()->get("user")->hasRole("User")){
            $agents->where("r.petitioner_id","=",session()->get("user")->id);
            $agents->whereNotIn('r.state_request_id', [5]);

            $petitioners->where("r.petitioner_id","=",session()->get("user")->id);
            $petitioners->whereNotIn('r.state_request_id', [5]);


        }else if(session()->get("user")->hasRole("Agent")) {
            $agents->where("r.petitioner_id","=",session()->get("user")->id);
            $agents->orWhere("r.agent_id","=",session()->get("user")->id);
            $agents->whereNotIn('r.state_request_id', [5]);


            $petitioners->where("r.petitioner_id","=",session()->get("user")->id);
            $petitioners->orWhere("r.agent_id","=",session()->get("user")->id);
            $petitioners->whereNotIn('r.state_request_id', [5]);

        }

        $data["agents"] = $agents->groupBy("id","name")->get();
        $data["petitioners"] = $petitioners->groupBy("id","name")->get();


        return view('pages.request',$data);
    }

    public function list(Request $request){

        
        $rules = [
            "ticket" => "nullable",
            "f_type" => "numeric",
            "f_state" =>"numeric",
            "f_petitioner" =>"numeric",
            "f_agent" =>"numeric",
            "f_date_init" => "required|date_format:Y-m-d",
            "f_date_end" => "required|date_format:Y-m-d|after_or_equal:f_date_end"
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
            "r.petitioner_id",
            "r.agent_id",
            "r.type_request_id",
            "r.state_request_id",
            "r.priority_request_id",
            "r.satisfaction_request_id",
            "r.register_date",
            DB::raw("CONCAT(p_peti.name , ' ',p_peti.last_name) as petitioner"),
            "r.description",
            DB::raw("CONCAT(p_agent.name , ' ',p_agent.last_name) as agent"),
            "type.name as type",
            DB::raw("IFNULL(prio.name , 'Sin Asignar') as priority_request"),
            DB::raw("CASE 
            WHEN ISNULL(maximum_minutes) THEN 0
            WHEN NOW() > tentative_end_date THEN 100
            ELSE 
            TRUNCATE ((TIMESTAMPDIFF(MINUTE, start_date, NOW()) / maximum_minutes )* 100, 0)
            END AS porcent_progress"),
            "st.name as state_request",
            DB::raw("IFNULL(sa.name , 'Sin Asignar') as satisfaction_request")
        ]);

        if(session()->get("user")->hasRole("User")){
            $requests->where("r.petitioner_id","=",session()->get("user")->id);
        }else if(session()->get("user")->hasRole("Agent")) {

            $requests->where(function($query) {
                $query->where("r.petitioner_id","=",session()->get("user")->id)->orWhere("r.agent_id","=",session()->get("user")->id);
            });
            //$requests->where("r.petitioner_id","=",session()->get("user")->id);
            //$requests->orWhere("r.agent_id","=",session()->get("user")->id);
        }

        if($request->ticket){
            $requests->where("r.id", "LIKE", "%{$request->ticket}%");
        }
        if($request->f_type){
            $requests->where("r.type_request_id","=",$request->f_type);
        }
        if($request->f_state){
            $requests->where("r.state_request_id","=",$request->f_state);
        }
        if($request->f_petitioner){
            $requests->where("r.petitioner_id","=",$request->f_petitioner);
        }
        
        if($request->f_agent){
            $requests->where("r.agent_id","=",$request->f_agent);
        }
        if($request->f_date_init && $request->f_date_end ){
            $requests->whereBetween( DB::raw("DATE(r.register_date)"),[$request->f_date_init, $request->f_date_end]);
        }


        if(! session()->get("user")->hasRole("Admin")){
            $requests->whereNotIn('r.state_request_id', [5]);
        }
        

        $requests->orderBy('r.id', 'DESC');
        $requests->get();



        //->get();


        return Datatables::of($requests)
         //->addColumn('acciones', "usuario_opciones")
         ->addColumn('options', function($row){
            $options = "<div class='dropdown no-arrow'>
            <button type='button' class='btn btn-primary btn-sm dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'><i class='fa fa-cogs'></i></button>
            <div class='dropdown-menu dropdown-menu-right shadow animated--fade-in' aria-labelledby='dropdownMenuLink'>";
            $options .= " <a class='dropdown-item' href='". route("solicitudes.view",["solicitude" => $row->id])  ."'>Ver</a>";

            
            if($row->state_request_id == 1){
                if(session()->get("user")->hasRole("User") || session()->get("user")->hasRole("Admin")){
                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Editar</a>";
                } 
                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Comentar</a>";

                if( session()->get("user")->hasRole("User") || session()->get("user")->hasRole("Admin")){
                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Anular</a>";
                }
                if ( session()->get("user")->hasRole("Agent") || session()->get("user")->hasRole("Admin") ){

                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Reasignar</a>";
                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Aprobar</a>";
                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Rechazar</a>";
                }
            }
            if($row->state_request_id == 2){

                $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Comentar</a>";

                if ( session()->get("user")->hasRole("Agent") || session()->get("user")->hasRole("Admin") ){
                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Reasignar</a>";
                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Detener</a>";
                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Cerrar</a>";
                }
            }
            if($row->state_request_id == 3){
                $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Comentar</a>";

                if ( session()->get("user")->hasRole("Agent") || session()->get("user")->hasRole("Admin") ){
                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Anular</a>";
                    $options .= " <a class='dropdown-item' href='". route("solicitudes.show",["solicitude" =>  $row->id])  ."'>Reanudar</a>";
                }
            }
        

            $options .= " <a class='dropdown-item' href=''>Imprimir</a>";
            $options .= "</div>
            </div>";
            return $options;

        })
        ->addColumn('state', function($row){
                
            switch ($row->state_request_id) {
                case 1:
                    $color = "info";
                    break;
                case 2:
                    $color = "warning";
                    break;
                case 3:
                    $color = "secondary";
                    break;
                case 4:
                    $color = "danger";
                    break;
                case 5:
                    $color = "danger";
                    break;
                case 6:
                    $color = "success";
                    break;
            }
            
            return "<span class='badge badge-".$color ."'>".$row->state_request ."</span>";
        })
        ->addColumn('progress', function($row){
            
            return  "<span class='badge badge-light'>".$row->porcent_progress."% </span>
                    <div class='progress'>
                        <div class='progress-bar progress-bar-striped bg-primary' role='progressbar' style='width:".$row->porcent_progress ."%' aria-valuenow='".$row->porcent_progress ."' aria-valuemin='0' aria-valuemax='100'></div>
                    </div>";
        })
        ->addColumn('priority', function($row){

            switch ($row->priority_request_id) {
                case null:
                    $color = "light";
                    break;
                case 1:
                    $color = "danger";
                    break;
                case 2:
                    $color = "warning";
                    break;
                case 3:
                    $color = "success";
                    break;
                case 4:
                    $color = "info";
                    break;
            }
           
            return "<span class='badge badge-".$color ."'>".$row->priority_request ."</span>";

        })        
        ->addColumn('satisfaction', function($row){

            switch ($row->satisfaction_request_id) {
                case null:
                    $color = "light";
                    break;
                case 1:
                    $color = "success";
                    break;
                case 2:
                    $color = "success";
                    break;
                case 3:
                    $color = "Warning";
                    break;
                case 4:
                    $color = "danger";
                    break;
                case 5:
                    $color = "danger";
                    break;
            }
           
            return "<span class='badge badge-".$color ."'>".$row->satisfaction_request ."</span>";

        })
        
        
        ->rawColumns(["options","state","progress","priority","satisfaction"])
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
  
        
        $data["action"] = "create"; 
        $data["title"] = "Crear Solicitud";
        $data["title_html"] = "Crear Solicitud";
        $data["title_module"] = "Crear Solicitud" ;

        
        $data["types"]  = TypeRequest::select(["id","name"])->where("isActive","=",true)->get();

        $petitioners = User::from('users as u')
        ->select(["u.id as id","p.name as name"])
        ->join("profiles as p","u.id", "=","p.user_id")
        ->whereNotIn('u.state_user_id', [3]);


        $agents= User::from('users as u')
        ->select(["u.id as id","p.name as name"])
        ->join("profiles as p","u.id", "=","p.user_id")
        ->join("type_user as tu", "u.id", "=","tu.user_id")
        ->whereNotIn('u.state_user_id', [3])
        ->where("tu.isActive","=",true);



        if(session()->get("user")->hasRole("User")){            
            $petitioners->where("u.id","=",  session()->get("user")->id);
            
        }
        //listar los agentes por configuracion

        $data["agents"] = $agents->groupBy("id","name")->get();
        $data["petitioners"] = $petitioners->groupBy("id","name")->get();

        return view('pages.request_create_edit',$data);
        


        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
        
            $rules = [
                "type_request_id" => "required|numeric",
                "agent_id" => "required|numeric",
                "petitioner_id" => "required|numeric",
                "title" => "required",
                "description" => "required",
            ];
            

            
            $validator = Validator::make($request->input(),$rules);

            if($validator->fails()){

                $errores = $validator->errors()->getMessages();
                foreach ($errores as $key =>$value) {
                    $errores_formated[$key] = $value[0];
                }
                return response()->json(["data" => $errores_formated],422);
            
            }

            DB::beginTransaction();
            
            //$ultimo_id = is_null(Solicitud::latest()->first()) ? 0 : Solicitud::latest()->first()->id;
            
            $modelRequest = new ModelsRequest();
            $modelRequest->type_request_id = $request->type_request_id;
            $modelRequest->agent_id = $request->agent_id;
            $modelRequest->petitioner_id =  $request->petitioner_id;
            $modelRequest->title = $request->title;
            $modelRequest->description = $request->description;
            $modelRequest->register_id = session()->get("user")->id;
            $modelRequest->state_request_id = 1; // PENDIENTE
            $modelRequest->save();


            $comment = new Comment();
            $comment->user_id = session()->get("user")->id;
            $comment->title = "Solicitud Registrada";
            $comment->comment = "La solicitante del ticket es ". User::find($request->petitioner_id)->profile->name;
            $modelRequest->comments()->save($comment);
            DB::commit();

            $dataRequest = ModelsRequest::with("petitioner","agent","type")->find($modelRequest->id);
            SendEmailJob::dispatch($dataRequest)->afterCommit();
            //return response()->json(["data" => ["redirect" => "/solicitudes"]],200);
            /*            
            if ($request->hasFile('file')) {

                foreach($request->file as $archivo){
                    
                    $solicitud_archivo = new SolicitudArchivo();
                    $solicitud_archivo->solicitud_id = $solicitud->id;
                    $solicitud_archivo->nombre = pathinfo($archivo->getClientOriginalName(),PATHINFO_FILENAME);
                    $solicitud_archivo->extension = "." .$archivo->getClientOriginalExtension();
                    $solicitud_archivo->ruta =  $archivo->store('/','solicitudes');
                    $solicitud_archivo->save();   
                }
            }
            $historial = new  SolicitudHistorial();
            $historial->solicitud_id = $solicitud->id;
            $historial->trabajador_id = $solicitud->solicitante_id;
            $historial->titulo = "Solicitud Regsitrada";
            $historial->descripcion = "Solicitud Regsitrada";
            $historial->save();
            DB::commit();
            

            event(new SolicitudRegistrada($solicitud));
            */

            


        

        //return response()->json(["data" => ["redirect" => "/requests"]],200);
            


        }
        catch(Exception $ex){
            DB::rollback();
            return response()->json(["data" => [ "messageClient" => "Se presentó un error. Inténtelo mas tarde" , "messageServer"  => $ex->getMessage()]],500);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data["action"] = "show";
        $data["title"] = "Editar Solicitud";
        $data["title_html"] = "Editar Solicitud";
        $data["title_module"] = "Editar Solicitud" ;

        $data["request"]  = ModelsRequest::with("comments")->find($id);
        //$archivos = $solicitud->archivos()->Activo()->get();


        
        $types = TypeRequest::select(["id","name"])->where("isActive","=",true);

        $petitioners = User::from('users as u')
        ->select(["u.id as id","p.name as name"])
        ->join("profiles as p","u.id", "=","p.user_id")
        ->whereNotIn('u.state_user_id', [3]);


        $agents= User::from('users as u')
        ->select(["u.id as id","p.name as name"])
        ->join("profiles as p","u.id", "=","p.user_id")
        ->join("type_user as tu", "u.id", "=","tu.user_id")
        ->whereNotIn('u.state_user_id', [3])
        ->where("tu.isActive","=",true);



        if(session()->get("user")->hasRole("User")){            
            $petitioners->where("u.id","=",  $data["request"]->petitioner_id);
            $agents->where("u.id","=",  $data["request"]->agent_id);
            $types->where("id","=",  $data["request"]->type_request_id);
            
        }
        //listar los agentes por configuracion

        $data["agents"] = $agents->groupBy("id","name")->get();
        $data["petitioners"] = $petitioners->groupBy("id","name")->get();
        $data["types"] = $types->get();

       

        return view('pages.request_create_edit',$data);

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
        try{
            $rules = [
                "type_request_id" => "required|numeric",
                "agent_id" => "required|numeric",
                "petitioner_id" => "required|numeric",
                "title" => "required",
                "description" => "required",
                //"file" => "nullable"
            ];
    
            
            $validator = Validator::make($request->input(),$rules);
    
            if($validator->fails()){
    
                $errores = $validator->errors()->getMessages();
                foreach ($errores as $key =>$value) {
                    $errores_formated[$key] = $value[0];
                }
                return response()->json(["data" => $errores_formated],422);
            }

            DB::beginTransaction();

            $modelRequest = ModelsRequest::find($id);
            $modelRequest->type_request_id = $request->type_request_id;
            $modelRequest->agent_id = $request->agent_id;
            $modelRequest->petitioner_id =  $request->petitioner_id;
            $modelRequest->title = $request->title;
            $modelRequest->description = $request->description;
            $modelRequest->save();
            
            /*
            if ($request->hasFile('file')) {


                foreach($request->file as $archivo){
                    
                    $solicitud_archivo = new SolicitudArchivo();
                    $solicitud_archivo->solicitud_id = $solicitud->id;
                    $solicitud_archivo->nombre = pathinfo($archivo->getClientOriginalName(),PATHINFO_FILENAME);
                    $solicitud_archivo->extension = "." .$archivo->getClientOriginalExtension();
                    $solicitud_archivo->ruta =  $archivo->store('/','solicitudes');
                    $solicitud_archivo->save();   
                }
            }

            if ($request->has('file_id')) {
                
                
                for ($i=0; $i < count($request->file_id)  ; $i++) { 
                    $solicitud_archivo = SolicitudArchivo::find($request->file_id[$i]);
                    $solicitud_archivo->archivo_estado_id = $request->file_estado[$i];
                    $solicitud_archivo->save();
                }
                
            }
            */

            DB::commit();
            return response()->json(["data" => ["redirect" => "/solicitudes"]],200);


        }catch(Exception $ex){
            DB::rollback();
            return response()->json(["data" => [ "messageClient" => "Se presento un error. Inténtelo mas tarde" , "messageServer"  => $ex->getMessage()]],500);

        }
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

    public function view($id){

        $data["action"] = "view";
        $data["title"] = "Ver Solicitud";
        $data["title_html"] = "Ver Solicitud";
        $data["title_module"] = "Ver Solicitud" ;

        $request = ModelsRequest::find($id);
        $data["request"] = $request;

        $comments = $request->comments()->with("user")->orderBy('created_at', 'DESC')->get();
        $data["comments"] = $comments;

        $types = TypeRequest::select(["id","name"])->where("isActive","=",true);

        $petitioners = User::from('users as u')
        ->select(["u.id as id","p.name as name"])
        ->join("profiles as p","u.id", "=","p.user_id")
        ->whereNotIn('u.state_user_id', [3]);


        $agents= User::from('users as u')
        ->select(["u.id as id","p.name as name"])
        ->join("profiles as p","u.id", "=","p.user_id")
        ->join("type_user as tu", "u.id", "=","tu.user_id")
        ->whereNotIn('u.state_user_id', [3])
        ->where("tu.isActive","=",true);



        if(session()->get("user")->hasRole("User")){            
            $petitioners->where("u.id","=",  $data["request"]->petitioner_id);
            $agents->where("u.id","=",  $data["request"]->agent_id);
            $types->where("id","=",  $data["request"]->type_request_id);
            
        }
        //listar los agentes por configuracion

        $data["agents"] = $agents->groupBy("id","name")->get();
        $data["petitioners"] = $petitioners->groupBy("id","name")->get();
        $data["types"] = $types->get();

        return view('pages.request_create_edit',$data);





    }

    public function agents($type_id){
        try{

            $agents= User::from('users as u')
            ->select(["u.id as id","p.name as name"])
            ->join("profiles as p","u.id", "=","p.user_id")
            ->join("type_user as tu", "u.id", "=","tu.user_id")
            ->whereNotIn('u.state_user_id', [3])
            ->where("tu.isActive","=",true)
            ->where("tu.isDefault","=",true)
            ->where("tu.type_request_id","=",$type_id)
            ->get();

            return response()->json(["data" => $agents],200);

        }catch(Exception $ex){
            return response()->json(["data" => [ "messageClient" => "Se presento un error. Inténtelo mas tarde" , "messageServer"  => $ex->getMessage()]],500);

        }
        //TypeRequest::find($type_id)->with("users")->where("isActive","=","1")->get();



    }

}
