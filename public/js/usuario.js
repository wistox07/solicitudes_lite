(function(){

    function getURL() {
        var getUrl = window.location;
        return getUrl.protocol + "//" +getUrl.host;
    
    }

    $.fn.dataTable.ext.errMode = 'throw';


    $('#tabla_usuario').DataTable({
        responsive:true,
        autoWidth:false,
        pageLength : 5,
        lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'Todos']],
        "ajax":{
            "url" : getURL()+"/users/list",
            "type" : "GET",
            "data" : function(d) {
                d.usuario_id = $("select[name=f_usuario]").val(),
                d.perfil_id = $("select[name=f_perfil]").val(),
                d.usuario_estado_id = $("select[name=f_estado]").val(),
                d.fecha_registro_inicio = $("input[name=f_fecha_registro_inicio]").val(),
                d.fecha_registro_fin = $("input[name=f_fecha_registro_fin]").val(),
                d.busqueda = $("input[name=f_busqueda").val()

            },
        },
        "columns": [
            { "data": "nombres" },
            { "data": "apellidos" },
            { "data": "usuario" },
            { "data": "correo" },
            { "data": "estado" },
            { "data": "perfil" },
            { "data": "fecha_registro" },
            { "data": "fecha_actualizacion" },
            { "data": "acciones"}
        ],
        'columnDefs': [
            {
                'class': 'text-center'
            },
            {
                'targets': [8],
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

    function buscarTrabajadorDNI(dni){
       
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'GET',
            url: getURL()+`/workers/search/${dni}`,
            beforeSend: function(){
                $("input[name=trabajador_id]").val('');
                $("input[name=nombres]").val('');
                $("input[name=apellido_paterno]").val('');
                $("input[name=apellido_materno]").val('');

                $("input[name=usuario]").val('');
                $("input[name=correo]").val('');
                $("input[name=contraseña]").val('');
                $("input[name=repetir_contraseña]").val('');
                $("select[name=perfil_id]").val('');
                $("select[name=usuario_estado_id]").val('');
                


            },
            success: function(response){

                $("input[name=usuario]").prop( "readonly", false );
                $("input[name=correo]").prop( "readonly", false );
                $("input[name=contraseña]").prop( "readonly", false );
                $("input[name=repetir_contraseña]").prop( "readonly", false );
                $("select[name=perfil_id]").prop( "disabled", false );
                $("select[name=usuario_estado_id]").prop( "disabled", false );
                $(".ver-contraseña").prop( "disabled", false );
                $("#guardar").prop( "disabled", false );

                
                

                data = response.data;
                $("input[name=trabajador_id]").val(data.id);
                $("input[name=nombres]").val(data.nombres);
                $("input[name=apellido_paterno]").val(data.apellido_paterno);
                $("input[name=apellido_materno]").val(data.apellido_materno);


                   
            },
            error: function (request, status, error) {
                $("input[name=numero_documento]").val('');

                $("input[name=usuario]").prop( "readonly", true );
                $("input[name=correo]").prop( "readonly", true );
                $("input[name=contraseña]").prop( "readonly", true );
                $("input[name=repetir_contraseña]").prop( "readonly", true );
                $("select[name=perfil_id]").prop( "disabled", true );
                $("select[name=usuario_estado_id]").prop( "disabled", true );
                $(".ver-contraseña").prop( "disabled", false );
                $("#guardar").prop( "disabled", true );

                data = request.responseJSON.data;
                swal({
                    title: "Error",
                    text: data.messageClient,
                    icon: "error",
                });
                
            }
        });
    }

    function registrarUsuario(){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'POST',
            url: getURL()+'/users',
            data: $("#guardar_usuario").serialize(),
            statusCode:{
                422: function(request, status, error){
                    //422 -- ERRROR DE VALIDACION
                    var data = request.responseJSON.data;
                    $("#guardar_usuario").validate().showErrors(data);
                    for (const prop in data) {                                                
                        $(`input[name=${prop}]`).addClass("is-invalid");
                      }
                    
                },
                200: function(response){
                    let redirect = response.data.redirect
                    swal({
                        title: "Usuario  Registrado",
                        text: "El usuario fue registrado exitosamente",
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
                        title: "Error al registrar",
                        text: data.messageClient,
                        icon: "error",
                    });
                    
                }


            }
        });
    }

    function actualizarUsuario(token){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'PUT',
            url: getURL()+`/users/${token}`,
            data: $("#guardar_usuario").serialize(),
            statusCode:{
                422: function(request, status, error){
                    //422 -- ERRROR DE VALIDACION
                    var data = request.responseJSON.data;
                    $("#guardar_usuario").validate().showErrors(data);
                    for (const prop in data) {                                                
                        $(`input[name=${prop}]`).addClass("is-invalid");
                      }
                    
                },
                200: function(response){
                    let redirect = response.data.redirect
                    swal({
                        title: "Usuario Actualizado",
                        text: "El usuario fue actualizado exitosamente",
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
                        title: "Error al actualizar",
                        text: data.messageClient,
                        icon: "error",
                    });
                    
                }


            }
        });
    }
    function modificarUsuario(token,estado){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'PATCH',
            url: getURL()+`/users/update-status/${token}`,
            data : {usuario_estado_id : estado.usuario_estado_id},
            success: function(response){
                swal( {
                    title: estado.title,
                    text: estado.text,
                    icon: "success",
                    timer: 3000
                    });

                    let table = $('#tabla_usuario').DataTable();
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
    function eliminarUsuario(token){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'DELETE',
            url: getURL()+`/users/${token}`,
            success: function(response){
                swal( {
                        title: "Eliminar",
                        text: "Usuario eliminado satisfactoriamente",
                        icon: "success",
                        timer: 3000
                    });
                    let table = $('#tabla_usuario').DataTable();
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
 
    $("select[name=f_usuario]").on("change", function() {
        
        let table = $('#tabla_usuario').DataTable();
        table.ajax.reload();
        

    });
    $("select[name=f_perfil]").on("change", function() {
        let table = $('#tabla_usuario').DataTable();
        table.ajax.reload();

    });

    $("select[name=f_estado]").on("change", function() {
        let table = $('#tabla_usuario').DataTable();
        table.ajax.reload();

    });

    $("input[name=f_fecha_registro_inicio], input[name=f_fecha_registro_fin], input[name=f_busqueda]").on('blur', function() {
        
            let table = $('#tabla_usuario').DataTable();
			table.ajax.reload();
        
    });

    $('#tabla_usuario tbody').on( 'click', '#eliminar_usuario', function () {
        var token = $(this).data("id");
        var estado = {
            usuario_estado_id : 3,
            title: "Eliminar",
            text: "Usuario eliminado satisfactoriamente",
        }
            swal({
                title: "Eliminar",
                text: "¿Está seguro de eliminar al usuario?",
                icon: "warning",
                buttons: {
                    cancel: 'Cancelar',
                    delete: 'Sí'
                }
            }).then(function(isConfirm){
                if (isConfirm) {
                    eliminarUsuario(token);
                }
            });
    });
    $('#tabla_usuario tbody').on( 'click', '#desactivar_usuario', function () {
        var token = $(this).data("id");
        var estado = {
            usuario_estado_id : 2,
            title: "Desactivar",
            text: "Usuario desactivado satisfactoriamente",
        }
            swal({
                title: "Desactivar",
                text: "¿Está seguro de desactivar al usuario?",
                icon: "warning",
                buttons: {
                    cancel: 'Cancelar',
                    delete: 'Sí'
                }
            }).then(function(isConfirm){
                if (isConfirm) {
                    modificarUsuario(token,estado);
                }
            });
    });
    $('#tabla_usuario tbody').on( 'click', '#activar_usuario', function () {
        var token = $(this).data("id");
        var estado = {
            usuario_estado_id : 1,
            title: "Activar",
            text: "Usuario activado satisfactoriamente",
        }
            swal({
                title: "Activar",
                text: "¿Está seguro de activar al usuario?",
                icon: "warning",
                buttons: {
                    cancel: 'Cancelar',
                    delete: 'Sí'
                }
            }).then(function(isConfirm){
                if (isConfirm) {
                    modificarUsuario(token,estado);
                }
            });
    });
    $('#tabla_usuario tbody').on( 'click', '#restaurar_usuario', function () {
        var token = $(this).data("id");
        var estado = {
            usuario_estado_id : 1,
            title: "Restaurar",
            text: "Usuario restaurado satisfactoriamente",
        }
            swal({
                title: "Restaurar",
                text: "¿Está seguro de restaurar al usuario?",
                icon: "warning",
                buttons: {
                    cancel: 'Cancelar',
                    delete: 'Sí'
                }
            }).then(function(isConfirm){
                if (isConfirm) {
                    modificarUsuario(token,estado);
                }
            });
    });

    $("#buscar_dni").on("click",function(){
        if ($("input[name=numero_documento]").val().length >=1 ) {
            let dni = $("input[name=numero_documento]").val();
            buscarTrabajadorDNI(dni)
        }

    })
    $(".ver-contraseña").on("click", function(){
        let tipo =  ($(this).prev().attr("type") == "text") ? "password" : "text";
        let clase  = ($(this).children().attr("class") == "fa-solid fa-lock") ? "fa-solid fa-lock-open" : "fa-solid fa-lock";
        $(this).prev().attr("type", tipo);
        $(this).children().attr("class",clase);
    })

    $.validator.addMethod("formAlphanumeric", function (value, element) {
        var pattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%&*])[a-zA-Z0-9!@#$%&*]+$/;
        return this.optional(element) || pattern.test(value);
      }, "El campo contraseña debe tener como mínimo una mayúscula , una minúscula, un dígito y un caracter especial");

    $("#guardar_usuario").validate({
       
        rules: {
            usuario: { required: true },
            correo: { required : true , email : true},
            contraseña: {formAlphanumeric: true , minlength: 8},
            repetir_contraseña: { required: true , equalTo: "#password"},
            perfil_id: { required:true },
            usuario_estado_id: { required: true},
            numero_documento: {required:true},
            nombres:{ required: true},
            apellido_paterno:{ required: true},
            apellido_materno: { required: true},
            fecha_registro:{required: true},
            fecha_actualizacion:{required:true}

        },
        messages: {
            usuario:{
                required: "Campo obligatorio",
            },
            correo:{
                required: "Campo obligatorio",
                email : "Ingrese un formato valido para el correo"
            },
            contraseña:{
                
                minlength : "El campo debe tener minimo 8 caracteres",
            },
            repetir_contraseña:{
                required: "Campo obligatorio",
                equalTo : "Las contraseñas no coinciden"
            },
            perfil_id:{
                required: "Campo obligatorio",
            },
            usuario_estado_id:{
                required: "Campo obligatorio",
            },
            numero_documento:{
                required: "Campo obligatorio"
            },
            nombres:{
                required: "Campo obligatorio"
            },
            apellido_paterno:{
                required: "Campo obligatorio"
            },
            apellido_materno:{
                required: "Campo obligatorio"
            },
            estado_id:{
                required: "Campo obligatorio"
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
                registrarUsuario()
            }
            else {
                $token = $("input[name=usuario_id]").val();
                actualizarUsuario($token);
            }
        }
    });
})();