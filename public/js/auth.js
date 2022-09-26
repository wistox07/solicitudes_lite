(function(){
    function getURL() {
        var getUrl = window.location;
        return getUrl.protocol + "//" +getUrl.host;
    
    }
    function logear(){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $("meta[name=csrf-token]").attr("content")
            },
            type:'POST',
            url:  getURL() + '/authentication/login',
            data:{
               email : $("input[name=email]").val(),
               password: $("input[name=password]").val()
            },
            beforeSend: function() {
                $("#message").hide();
                $("#btnlogin").html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Loading...")
                $("#email").removeClass("is-invalid");
                $("#password").removeClass("is-invalid");

            },
            complete: function(){
                $("#btnlogin").html("Login")

            },
            statusCode:{
                422: function(request, status, error){
                    //422 -- ERRROR DE VALIDACION
                    var data = request.responseJSON.data;
                    $("#btnlogin").validate().showErrors(data);
                    for (const prop in data) {                                                
                        $(`input[name=${prop}]`).addClass("is-invalid");
                      }
                    
                },
                200: function(response){
                    //200 -- LOGEADO
                   var redirect = response.data.redirect;
                   window.location.href = getURL() + `${redirect}`;
                   
                },
                403: function(request,status, error){
                    //403 -- CREDECIALES INCORRECTAS
                    data = request.responseJSON.data;
                    $("#message").text(data.messageClient);
                    $("#message").show();
                    
                },
                500: function(request, status, error){
                    //500 -- ERRROR DEL SERVIDOR
                    data = request.responseJSON.data;
                    $("#message").text(data.messageClient)
                    $("#message").show();
                    
                }


            }
        });
    }
    $("#login").validate({
        rules: {
            // ADJUNTAR DOCUMENTOS
            email: { required: true },
            password: {required : true}
        },
        messages: {
            email:{
                required: "Campo obligatorio"
            },
            password:{
                required: "Campo obligatorio"
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
            logear();
        }
    });




})();