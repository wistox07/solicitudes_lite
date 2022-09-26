@extends("pages.base")

@section("links")

<style>
    textarea{
        resize: none;
    }
</style>

@endsection

@section("scripts")
<script src="https://cdn.datatables.net/1.11.5/js/jquery.js" type="text/javascript" ></script>    
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="{{ URL::asset('js/requests_create_edit.js') }}"></script>
@endsection

@section("section")
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title_module }}</h1>
    </div>


    <form class="row"  id="save_request" novalidate autocomplete="off">
        <div class="col-12">
            <!-- Default Card Example -->
            <div class="card mb-4">
                <div class="card-header">
                    Datos Generales
                </div>

                <div class="card-body">
                    <div class="row">
                        @if ($action == "show")
                            <input type="hidden" name="request_id" value="{{ isset($request) ? $request->id : '' }}" >
                        @endif
                        <div class="col-12 col-lg-3 ">
                            <label for="type_request_id" class="form-label">Tipo</label>
                            <select  class="form-select"  name="type_request_id">
                                @if ($action == "create")
                                    <option selected value="">SELECCIONE</option>
                                @endif
                                @foreach ($types as $type)
                                        <option {{ isset($request) && $request->type_request_id == $type->id ? 'selected' : '' }} value="{{$type->id}}">{{$type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row  g-3 mt-0">
                        <div class="col-12 col-lg-3 ">
                            <label for="agent_id" class="form-label">Agente</label>
                            <select  class="form-select"  name="agent_id">
                                @if ($action == "create")
                                    <option selected value="">SELECCIONE</option>
                                @else
                                    @foreach ($agents as $agent)
                                        <option {{ isset($request) && $request->agent_id == $agent->id ? 'selected' : '' }} value="{{$agent->id}}">{{$agent->name}}</option>
                                    @endforeach
                                @endif       
                            </select>
                        </div>
                        <div class="col-12 col-lg-3 ">
                            <label for="petitioner_id" class="form-label">Solicitante</label>
                            <select  class="form-select"  name="petitioner_id" >
                                @if ($action == "create" && (session()->get("user")->hasRole("Agent") || session()->get("user")->hasRole("Admin")))
                                    <option selected value=>SELECCIONE</option>
                                @endif
                                @foreach ($petitioners as $petitioner)
                                        <option {{ isset($request) && $request->petitioner_id == $petitioner->id ? 'selected' : '' }} value="{{$petitioner->id}}">{{$petitioner->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row  g-3 mt-0">
                        <div class="col-12 col-lg-6 ">
                            <label for="title" class="form-label">Titulo</label>
                            <textarea rows="3" class="form-control" name="title">{{ isset($request) ? $request->title: '' }}</textarea>
                        </div>
                        <div class="col-12 col-lg-6 ">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea rows="3" class="form-control" name="description">{{ isset($request) ? $request->description: '' }}</textarea>
                        </div>
                    </div>
                    <!--<form class="row g-3" id="guardar_solicitud" novalidate autocomplete="off">-->

                    <!--</form>-->
                </div>

            </div>
        </div>
        @if ($action == "create" || $action=="show")
            <div class="col-12">
                <button  data-action="{{ ($action=="create") ? 'create' : 'edit' }}" id="save" type="submit" class="btn btn-primary text-light"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
            </div>
        @endif
    </form>

</div>
@endsection