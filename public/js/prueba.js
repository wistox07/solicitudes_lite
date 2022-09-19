(function(){
    

    function getURL() {
        var getUrl = window.location;
        return getUrl.protocol + "//" +getUrl.host;
    
    }

    function buscarTrabajadorDNI(dni){
    
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'GET',
            url: getURL()+`/workers/search/${dni}`,
            success: function(response){
               
               console.log(response);
                   
            }
        });

    }

    
    $("#login").validate({
        rules: {
            trabajador_numero_documento: { required: true },
        },
        messages: {
            trabajador_numero_documento:{
                required: "Campo obligatorio",
            }
        },
        errorElement: 'div',
        errorClass: "custom-error",
        errorPlacement: function (error, element) {
                error.insertAfter(element);
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function () { //console.log('submitHandler')
            let dni = $("input[name=numero_documento]").val();
            buscarTrabajadorDNI(dni);
        }
    });
    

   



})();