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
    
    function removeItemFromArray (array, item) {
        var i = array.indexOf(item);

        if (i !== -1) {
            array.splice(i, 1);
        }
    }

    function getFileExtension(fileName) {
        return fileName.slice((fileName.lastIndexOf('.') - 1 >>> 0) + 2);
    }
    function convertFileFize(_size) {
        var fSExt = new Array('Bytes', 'KB', 'MB', 'GB'),
            i = 0;

        while (_size > 900) {
            _size /= 1024;
            i++;
        }

        return (Math.round(_size * 100) / 100) + ' ' + fSExt[i];
    }
    function obtenerSubtipos(tipo_id){

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'GET',
            url: getURL()+`/subtypes/list/${tipo_id}`,
            success: function(response){
                subtipos = response.data;
                subtipos.forEach(function(subtipo) {
                    $("select[name=solicitud_subtipo_id]").append(`<option value=${subtipo.id}>${subtipo.nombre}</option>`);
                  });
                   
            },
            error: function (request, status, error) {
                console.log("ga");
            }
        });
    }
    function obtenerResponsable(tipo_id){

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'GET',
            url: getURL()+`/workers/search-by-type/${tipo_id}`,
            success: function(response){
              responsables = response.data;
              responsables.forEach(function(responsable) {
                $("select[name=responsable_id]").append(`<option selected value=${responsable.id}>${responsable.nombres}</option>`);
              });
                                 
            },
            error: function (request, status, error) {
                console.log("ga");
            }
        });
    }
    /*
    function obtenerResponsable(tipo_id){

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'GET',
            url: getURL()+`/workers/search-by-type/${tipo_id}`,
            success: function(response){
              responsable = response.data;
              //console.log(responsable);
              $("select[name=responsable_id]").val(responsable.id)
                   
            },
            error: function (request, status, error) {
                console.log("ga");
            }
        });
    }
    */
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
                    /*
                    let redirect = response.data.redirect
                    swal({
                        title: "Solicitud  Registrada",
                        text: "La solicitud fue registrada exitosamente",
                        timer: 2000,
                        icon: "success",
                    }).then(function(){
                        window.location.href = getURL() + redirect;
                    });
                    */
                   
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
                    /*
                    let redirect = response.data.redirect
                    swal({
                        title: "Solicitud  Registrada",
                        text: "La solicitud fue registrada exitosamente",
                        timer: 2000,
                        icon: "success",
                    }).then(function(){
                        window.location.href = getURL() + redirect;
                    });
                    */
                   
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

    $("input[name=f_date_init], input[name=f_date_end], input[name=ticket]").on('blur', function() {
        
        let table = $('#table_requests').DataTable();
        table.ajax.reload();
    
    });
    
    $("select[name=f_type], select[name=f_petitioner], select[name=f_agent]").on("change", function() {

        //$("select[name=solicitud_subtipo_id]").empty();
        //$("select[name=solicitud_subtipo_id]").append("<option value=''>SELECCIONE</option>");

        //$("select[name=responsable_id]").empty();
        //$("select[name=responsable_id]").append("<option value=''>SELECCIONE</option>");

        //if($("select[name=f_type]").val() !== "")
       // {
            let table = $('#table_requests').DataTable();
            table.ajax.reload();
            //obtenerSubtipos($("select[name=solicitud_tipo_id]").val());
            //obtenerResponsable($("select[name=solicitud_tipo_id]").val());
       // }
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
    $('#agregar_archivo').on('click', function (e) {
        //var i = $('input:file').length;
        $('input[name="file[]"]:last').click();
        e.stopPropagation();
        //console.log("hola");
    });
    
    $('.fileContainer').on('change', 'input[name^=file]', function (e) {
        var file, row, fileExists, valid, span1, span2;
        var addNewInputFile = false;
        console.log('gg');
        $(this.files).each(function () {
            file = this;
            fileExists = false;

            if ($.inArray(file.name, gUploadedFiles) !== -1) {
                fileExists = true;
            }

            if (!fileExists) {
                row = $('.file.base').clone().removeClass('base').addClass('add').show();

                row.find('td:eq(0) span').html($('.file.add').length + 1);
                row.find('td:eq(1) span').html(file.name);
                row.find('td:eq(2) span').html(getFileExtension(file.name));
                row.find('td:eq(3) span').html("17/07/2022");

                /*valid = validateDocument(file.name.split('.')[0]);

                if (valid.error) {
                    span1 = '<span class="label label-circle label-danger"></span>';
                    span2 = '<span class="error" data-toggle="tooltip" title="' + valid.msg + '">NO VALIDO</span>';

                    row.addClass('error');
                } else {
                    span1 = '<span class="label label-circle label-valid"></span>';
                    span2 = '<span>VALIDO</span>';
                }*/
                span1 = '<span class="label label-circle label-valid"></span>';
                span2 = '<span>VALIDO</span>';

                row.find('td:eq(4)').html(span1 + span2);

                $('.file.base').closest('tbody').append(row);

                gUploadedFiles.push(file.name);
                addNewInputFile = true;
            }
        });


        if (addNewInputFile) {
            $('.fileContainer').append('<input type="file" name="file' + '[]" accept=".pdf" >');
        }

        if (gUploadedFiles.length && $('.no-data').hasClass('hide') === false) {
            $('.no-data').addClass('hide');
            $('.btnEnviarInformacion').prop('disabled', false);
        }

        //BlankonApp.handleTooltip();

        e.stopPropagation();
    });

    $('#tabla_adjuntos').on('click', 'a[name=btnDelete]', function (e) {
        var fileName = $(this).closest('tr').find('td:eq(1) span').html();
        var fila = $(this).closest('tr');
        if(fila.hasClass("store")){
            fila.find('input[name^=file_estado]').val(2);
            $(this).closest('tr').hide();
            //<input name="file_estado[]" type="hidden" value="{{$archivo->archivo_estado_id}}" ></input>
        }
        else{
            var fileAnterior = ($('input[name="file[]"]').length -1) - 1;
            $('input[name="file[]"]')[fileAnterior].remove();
            $(this).closest('tr').remove();
            
        }
        removeItemFromArray(gUploadedFiles, fileName);
        

        $('.file.add').each(function (index) {
            $(this).find('td:eq(0) span').html(index + 1)
        });


        e.stopPropagation();
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