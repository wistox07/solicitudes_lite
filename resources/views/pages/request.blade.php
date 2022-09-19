@extends("pages.base")

@section("links")
<link href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css" rel="stylesheet">
<style>
.table thead tr th , tbody , tfoot {
            font-size: 14.4px;
        }
.table-middle tr th, .table-middle tr td{
    vertical-align: middle !important;

}
</style>

<link href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section("scripts")
<script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript" src="{{ URL::asset('js/requests.js') }}"></script>
@endsection

@section("section")
<div class="container-fluid">

    <!-- Page Heading -->
    <!-- Content Row -->
    <div class="card mb-4">
        <div class="card-body">
        <h5 class="text-gray-800">{{$title_module}}</h5>
            <div class="row g-2 mb-1">
                    <div class="col-12 col-lg-6 col-xl-2">
                            <label class="form-label">Busqueda</label>
                            <input class="form-control form-control-sm" placeholder="N° Ticket" type="text" name="ticket">
                    </div>
                    <div class="col-12 col-lg-6 col-xl-2">
                        <label class="form-label">Tipo</label>
                        <select class="form-select form-select-sm" name="f_type">
                        <option selected value="0">Tipo</option>
                        @foreach ($types as $type)
                                <option value="{{$type->id}}">{{$type->name}}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-6 col-xl-2">
                        <label class="form-label">Estado</label>
                        <select class="form-select form-select-sm" name="f_state">
                        <option selected value="0">Estado</option>
                        @foreach ($states as $state)
                                <option value="{{$state->id}}">{{$state->name}}</option>
                        @endforeach
                        </select>
                    </div>
                    {{-- @if (Auth::user()->perfil->id == "3") --}}
                    <div class="col-12 col-lg-6 col-xl-2">
                        <label class="form-label">Solicitante</label>
                        <select class="form-select form-select-sm" name="f_petitioner">
                        <option selected value="0">Solicitante</option>
                        @foreach ($petitioners as $petitioner)
                                <option value="{{$petitioner->id}}">{{$petitioner->name}}</option>
                        @endforeach
                        </select>
                    </div>

                    {{-- @if (Auth::user()->perfil->id == "3") --}}
                    <div class="col-12 col-lg-6 col-xl-2">
                        <label class="form-label">Encargado</label>
                        <select class="form-select form-select-sm" name="f_agent">
                        <option selected value="0">Encargado</option>
                        @foreach ($agents as $agent)
                                <option value="{{$agent->id}}">{{$agent->name}}</option>
                        @endforeach
                        </select>
                    </div>
            </div>
            <div class="row">
                        <div class="col-12 col-lg-6 col-xl-2">
                            <label class="form-label">Fecha Inicial Registro </label>
                            <input type="date" class="form-control form-control-sm" name="f_date_init" value="{{date("Y-01-01")}}">
                            
                        </div>
                        <div class="col-12 col-lg-6 col-xl-2">
                            <label class="form-label">Fecha Fin Registro</label>
                            <input type="date" class="form-control form-control-sm" name="f_date_end" value="{{date("Y-m-d")}}">
                        </div>
                        <div class="col align-self-end">
                            <a class="btn btn-primary btn-sm text-light" href='{{ route("solicitudes.create") }}' role="button"><i class="fa-solid fa-plus"></i>Nuevo</a>
                        </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="">
                    <table id="table_requests" class="display responsive table table-middle table-bordered table-default dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Solicitante</th>
                                <th>Solicitud</th>
                                <th>Encargado</th>
                                <th>Tipo</th>
                                <th>Prioridad</th>
                                <th>Avance</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Satisfacción</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>N°</th>
                                <th>Solicitante</th>
                                <th>Solicitud</th>
                                <th>Encargado</th>
                                <th>Tipo</th>
                                <th>Prioridad</th>
                                <th>Avance</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Satisfacción</th>
                                <th>Acción</th>
                            </tr>
                        </tfoot>
                    </table>
                    </div>

                </div>
            </div>
        </div>
    </div>









</div>
@endsection