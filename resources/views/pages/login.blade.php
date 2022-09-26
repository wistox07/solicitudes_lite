<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>SB Admin 2 - Login</title>

    <!-- Custom fonts for this template-->

    <link href=" {{ URL::asset('sb_admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ URL::asset('sb_admin/css/sb-admin-2.min.css')}}" rel="stylesheet">


</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <div class="alert alert-danger" id="message" role="alert" style="display:none;"></div>
                                    <form class="user" id="login" novalidate autocomplete="off">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user"
                                                name="email" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address...">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                name="password" placeholder="Password">
                                        </div>

                                        <button  type="submit" class="btn btn-primary btn-user btn-block" id="btnlogin" >Login</button>
                                    </form>

                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="register.html">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <script src="{{ URL::asset('sb_admin/vendor/jquery/jquery.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.js"></script>

    <script src="{{ URL::asset('sb_admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ URL::asset('sb_admin/vendor/jquery-easing/jquery.easing.min.js')}}"></script>
    <script src="{{URL::asset('sb_admin/js/sb-admin-2.min.js')}}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/auth.js') }}"></script>


</body>

</html>