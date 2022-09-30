(function(){

    function getURL() {
        var getUrl = window.location;
        return getUrl.protocol + "//" +getUrl.host;
    
    }

    $.fn.dataTable.ext.errMode = 'throw';

    var gUploadedFiles = new Array();

    $('#table_requests').DataTable({
        responsive:true,
        "serverSide" : true,
        "ajax":{
            "url" : getURL()+"/solicitudes/list",
            "type" : "GET",
            "data" : function(d) {
                d.ticket = $("input[name=ticket").val(),
                d.f_type = $("select[name=f_type]").val(),
                d.f_state = $("select[name=f_state]").val(),
                d.f_petitioner = $("select[name=f_petitioner]").val(),
                d.f_agent = $("select[name=f_agent]").val(),
                d.f_date_init = $("input[name=f_date_init]").val(),
                d.f_date_end = $("input[name=f_date_end]").val()
            },
        },
        "columns": [
            { "data": "id" },
            { "data": "petitioner"},
            { "data": "description"},
            { "data": "agent"},
            { "data": "type"},
            { "data": "priority"},
            { "data": "progress"},
            { "data": "state"},
            { "data": "register_date"},
            { "data": "satisfaction"},
            { "data": "options"}
        ],
        'columnDefs': [
            {                
                'targets': [4,6,7,8,9,10],
                'class': 'text-center'
            }
        ],
        fixedColumns: true,
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "_MENU_",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "",
            "sSearchPlaceholder": "Buscar",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        'order': [] //[[0, 'desc']]
    });

    function obtenerEncargado(subtipo_id){

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'GET',
            url: getURL()+`/workers/search-by-subtype/${subtipo_id}`,
            success: function(response){
                encargados = response.data.encargados;
                encargado_default = response.data.encargado_default;

                encargados.forEach(function(encargado) {
                  $("select[name=encargado_id]").append(`<option value=${encargado.id}>${encargado.nombres}</option>`);
                });
                $("select[name=encargado_id]").val(encargado_default.id)


            },
            error: function (request, status, error) {
                console.log("ga");
            }
        });
    }

    function registrarSolicitud(){
        var form = $("#guardar_solicitud")[0];
        var datos = new FormData(form);
        console.log(datos);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'POST',
            dataType   : 'json',
            url: getURL()+'/requests',
            cache : false,
            processData: false,
            contentType: false,
            data:  datos,
            statusCode:{
                422: function(request, status, error){
                    var data = request.responseJSON.data;
                    $("#guardar_solicitud").validate().showErrors(data);
                    for (const prop in data) {                                                
                        $(`input[name=${prop}]`).addClass("is-invalid");
                      }
                    
                },
                200: function(response){
                    let redirect = response.data.redirect
                    swal({
                        title: "Solicitud  Registrada",
                        text: "La solicitud fue registrado exitosamente",
                        timer: 2000,
                        icon: "success",
                    }).then(function(){
                        window.location.href = getURL() + redirect;
                    });
                },
                500: function(request, status, error){
                    //500 -- ERRROR DEL SERVIDOR
                    data = request.responseJSON.data;
                    swal({
                        title: "Error al registrar solicitud",
                        text: data.messageClient,
                        icon: "error",
                    });
                    
                }


            }
        });
    }
    
    function actualizarSolicitud(token){
        let form = $("#guardar_solicitud")[0];
        let datos = new FormData(form);
        console.log(datos);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'POST',
            dataType   : 'json',
            url: getURL()+`/requests/${token}`,
            cache : false,
            processData: false,
            contentType: false,
            data:  datos,
            statusCode:{
                422: function(request, status, error){
                    var data = request.responseJSON.data;
                    $("#guardar_solicitud").validate().showErrors(data);
                    for (const prop in data) {                                                
                        $(`input[name=${prop}]`).addClass("is-invalid");
                      }
                    
                },
                200: function(response){
                    let redirect = response.data.redirect
                    swal({
                        title: "Solicitud  Actualizada",
                        text: "La solicitud fue actualizada exitosamente",
                        timer: 2000,
                        icon: "success",
                    }).then(function(){
                        window.location.href = getURL() + redirect;
                    });                   
                },
                500: function(request, status, error){
                    //500 -- ERRROR DEL SERVIDOR
                    data = request.responseJSON.data;
                    swal({
                        title: "Error al actualizar solicitud",
                        text: data.messageClient,
                        icon: "error",
                    });
                    
                }


            }
        });

    }

    $("input[name=f_date_init], input[name=f_date_end], input[name=ticket]").on('blur', function() {
        
        let table = $('#table_requests').DataTable();
        table.ajax.reload();
    
    });
    
    $("select[name=f_type], select[name=f_petitioner], select[name=f_agent]").on("change", function() {

            let table = $('#table_requests').DataTable();
            table.ajax.reload();
    });


    $("select[name=solicitud_subtipo_id]").on("change", function() {


        $("select[name=encargado_id]").empty();
        $("select[name=encargado_id]").append("<option value=''>SELECCIONE</option>");

        if($("select[name=solicitud_subtipo_id]").val() !== "")
        {
            obtenerEncargado($("select[name=solicitud_subtipo_id]").val());
        }
    });



    $("select[name=f_state]").on("change", function() {
        let table = $('#table_requests').DataTable();
        table.ajax.reload();

    });

    $("#guardar_solicitud").validate({
       
        rules: {
            solicitud_tipo_id:{ required: true},
            solicitud_subtipo_id:{ required: true},
            responsable_id: { required: true},
            encargado_id: { required:true },
            solicitante_id: { required: true},
            titulo: {required:true },
            descripcion:{required: true}

        },
        messages: {
            solicitud_tipo_id:{
                required: "Campo obligatorio",
            },
            solicitud_subtipo_id:{
                required: "Campo obligatorio"
            },
            responsable_id:{
                required: "Campo obligatorio"
            },
            encargado_id:{
                required: "Campo obligatorio",
            },
            solicitante_id:{
                required: "Campo obligatorio",
            },
            titulo:{
                required: "Campo obligatorio"
            },
            descripcion:{
                required: "Campo obligatorio"
            }
        },
        errorElement: 'div',
        errorClass: "custom-error",
        errorPlacement: function (error, element) {

            if(element.parent().hasClass('input-group')){
                error.insertAfter( element.parent() );
            }else{
                error.insertAfter( element );
            }

        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function () { //console.log('submitHandler')
            
            if($('#guardar').data("accion") == "create"){
                registrarSolicitud()
            }
            else {
                $token = $("input[name=solicitud_id]").val();
                actualizarSolicitud($token);
            }
            
        }
    });
})();