(function(){

    function getURL() {
        var getUrl = window.location;
        return getUrl.protocol + "//" +getUrl.host;
    
    }

    $.fn.dataTable.ext.errMode = 'throw';


    $('#tabla_trabajador').DataTable({
        responsive:true,
        autoWidth:false,
        pageLength : 5,
        lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'Todos']],
        "ajax":{
            "url" : getURL()+"/workers/list",
            "type" : "GET",
            "data" : function(d) {
                d.area_id = $("select[name=f_area]").val(),
                d.trabajador_estado_id = $("select[name=f_estado]").val(),
                d.fecha_registro_inicio = $("input[name=f_fecha_registro_inicio]").val(),
                d.fecha_registro_fin = $("input[name=f_fecha_registro_fin]").val(),
                d.busqueda = $("input[name=f_busqueda").val()

            },
        },
        "columns": [
            { "data": "nombres" },
            { "data": "apellidos" },
            { "data": "numero_documento"},
            { "data": "area" },
            { "data": "estado" },
            { "data": "fecha_registro" },
            { "data": "fecha_actualizacion" },
            { "data": "acciones"}
        ],
        'columnDefs': [
            {
                'class': 'text-center'
            },
            {
                'targets': [7],
                'class': 'text-center'
            }
        ],
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



    function registrarTrabajador(){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'POST',
            url: getURL()+'/workers',
            data: $("#guardar_trabajador").serialize(),
            statusCode:{
                422: function(request, status, error){
                    var data = request.responseJSON.data;
                    $("#guardar_trabajador").validate().showErrors(data);
                    for (const prop in data) {                                                
                        $(`input[name=${prop}]`).addClass("is-invalid");
                      }
                    
                },
                200: function(response){
                    let redirect = response.data.redirect
                    swal({
                        title: "Trabajador  Registrado",
                        text: "El trabajador fue registrado exitosamente",
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
                        title: "Error al registrar trabajador",
                        text: data.messageClient,
                        icon: "error",
                    });
                    
                }


            }
        });
    }

    function actualizarTrabajador(token){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'PUT',
            url: getURL()+`/workers/${token}`,
            data: $("#guardar_trabajador").serialize(),
            statusCode:{
                422: function(request, status, error){
                    //422 -- ERRROR DE VALIDACION
                    var data = request.responseJSON.data;
                    $("#guardar_trabajador").validate().showErrors(data);
                    for (const prop in data) {                                                
                        $(`input[name=${prop}]`).addClass("is-invalid");
                      }
                    
                },
                200: function(response){
                    let redirect = response.data.redirect
                    swal({
                        title: "Trabajador Actualizado",
                        text: "El trabajador fue actualizado exitosamente",
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
                        title: "Error al actualizar al trabajador",
                        text: data.messageClient,
                        icon: "error",
                    });
                    
                }


            }
        });
    }

    function modificarTrabajador(token,estado){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'PATCH',
            url: getURL()+`/workers/update-status/${token}`,
            data : {trabajador_estado_id : estado.trabajador_estado_id},
            success: function(response){
                swal( {
                    title: estado.title,
                    text: estado.text,
                    icon: "success",
                    timer: 3000
                    });

                    let table = $('#tabla_trabajador').DataTable();
                    table.ajax.reload();
                   
            },
            error: function (request, status, error) {
                data = request.responseJSON.data;

                swal({
                    title: "Error",
                    text: data.messageClient,
                    icon: "error",
                });
                
            }
        });
    }
    
    $("select[name=f_area]").on("change", function() {
        
        let table = $('#tabla_trabajador').DataTable();
        table.ajax.reload();
        

    });
    $("select[name=f_estado]").on("change", function() {
        let table = $('#tabla_trabajador').DataTable();
        table.ajax.reload();

    });

    $("input[name=f_fecha_registro_inicio], input[name=f_fecha_registro_fin], input[name=f_busqueda]").on('blur', function() {
        
            let table = $('#tabla_trabajador').DataTable();
			table.ajax.reload();
        
    });
    $('#tabla_trabajador tbody').on( 'click', '#eliminar_trabajador', function () {
        var token = $(this).data("id");
        var estado = {
            trabajador_estado_id : 3,
            title: "Eliminar",
            text: "Trabajador eliminado satisfactoriamente",
        }
            swal({
                title: "Eliminar",
                text: "¿Está seguro de eliminar al trabajador?",
                icon: "warning",
                buttons: {
                    cancel: 'Cancelar',
                    delete: 'Sí'
                }
            }).then(function(isConfirm){
                if (isConfirm) {
                    modificarTrabajador(token,estado);
                }
            });
    });
    $('#tabla_trabajador tbody').on( 'click', '#desactivar_trabajador', function () {
        var token = $(this).data("id");
        var estado = {
            trabajador_estado_id : 2,
            title: "Desactivar",
            text: "Trabajador desactivado satisfactoriamente",
        }
            swal({
                title: "Desactivar",
                text: "¿Está seguro de desactivar al trabajador?",
                icon: "warning",
                buttons: {
                    cancel: 'Cancelar',
                    delete: 'Sí'
                }
            }).then(function(isConfirm){
                if (isConfirm) {
                    modificarTrabajador(token,estado);
                }
            });
    });
    $('#tabla_trabajador tbody').on( 'click', '#activar_trabajador', function () {
        var token = $(this).data("id");
        var estado = {
            trabajador_estado_id : 1,
            title: "Activar",
            text: "Trabajador activado satisfactoriamente",
        }
            swal({
                title: "Activar",
                text: "¿Está seguro de activar al trabajador?",
                icon: "warning",
                buttons: {
                    cancel: 'Cancelar',
                    delete: 'Sí'
                }
            }).then(function(isConfirm){
                if (isConfirm) {
                    modificarTrabajador(token,estado);
                }
            });
    });
    $('#tabla_trabajador tbody').on( 'click', '#restaurar_trabajador', function () {
        var token = $(this).data("id");
        var estado = {
            trabajador_estado_id : 1,
            title: "Restaurar",
            text: "Trabajador restaurado satisfactoriamente",
        }
            swal({
                title: "Restaurar",
                text: "¿Está seguro de restaurar al trabajador?",
                icon: "warning",
                buttons: {
                    cancel: 'Cancelar',
                    delete: 'Sí'
                }
            }).then(function(isConfirm){
                if (isConfirm) {
                    modificarTrabajador(token,estado);
                }
            });
    });
    

    $("#guardar_trabajador").validate({
       
        rules: {
            nombres:{ required: true},
            apellido_paterno:{ required: true},
            apellido_materno: { required: true},
            area_id: { required:true },
            trabajador_estado_id: { required: true},
            numero_documento: {required:true, minlength: 8},
            fecha_registro:{required: true},
            fecha_actualizacion:{required:true}

        },
        messages: {
            nombres:{
                required: "Campo obligatorio",
            },
            apellido_paterno:{
                required: "Campo obligatorio"
            },
            apellido_materno:{
                required: "Campo obligatorio"
            },
            area_id:{
                required: "Campo obligatorio",
            },
            trabajador_estado_id:{
                required: "Campo obligatorio",
            },
            numero_documento:{
                required: "Campo obligatorio",
                minlength : "El campo debe tener minimo 8 caracteres",
            },
            fecha_registro:{
                required: "Campo obligatorio"
            },
            fecha_actualizacion:{
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
                registrarTrabajador()
            }
            else {
                $token = $("input[name=trabajador_id]").val();
                actualizarTrabajador($token);
            }
            
        }
    });
})();