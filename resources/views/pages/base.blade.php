<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$title_html}}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ URL::asset('sb_admin/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <!--<link href="{{ URL::asset('sb_admin/css/sb-pro.css')}}" rel="stylesheet">-->
    <link href="{{ URL::asset('sb_admin/css/sb-admin-2.css')}}" rel="stylesheet">
    <link href="{{ URL::asset('sb_admin/css/sb-timeline.css')}}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/988e31fec1.js" crossorigin="anonymous"></script>
    
    <style>
        .custom{
            display:flex;
            flex-direction: column;
        }

        .custom-error {
            width: 100%;
            margin-top: .25rem;
            font-size: .875em;
            color: #dc3545;
        }    
    </style>



    @yield("links")
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <!--<li class="nav-item active">
                <a class="nav-link" href="/dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>-->
           
            <li class="nav-item {{ (request()->is('users*')) ? 'active' : '' }}">
                <a class="nav-link" href="/users">
                <i class="fa fa-user fa-fw"></i>
                    <span>Usuarios</span></a>
            </li>
            

            <li class="nav-item {{ (request()->is('workers*')) ? 'active' : '' }}">
                <a class="nav-link" href="/workers">
                <i class="fa-solid fa-user-tie fa-fw"></i>
                    <span>Trabajadores</span>
                </a>
            </li>
            <li class="nav-item {{ (request()->is('requests*')) ? 'active' : '' }}">
                <a class="nav-link" href="/requests">
                <i class="fa fa-book fa-fw"></i>
                    <span>Solicitudes</span></a>
            </li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="custom">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{-- Auth::user()->trabajador->nombres --}} {{-- Auth::user()->trabajador->apellido_paterno --}}</span>
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{-- Auth::user()->perfil->nombre --}}</span>
                                </div>
                                <img class="img-profile rounded-circle"
                                    src="{{URL::asset('sb_admin/img/undraw_profile.svg')}}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                @yield("section")

                <!-- /.container-fluid -->


            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; {{ date("Y") }} &mdash; MixCode </span>
                        
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Listo para cerrar sesion?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Seleccione "Cerrar" si esta listo para  cerrar la sesion actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{-- route("logout") --}}">Cerrar</a>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap core JavaScript-->

    <script src="{{ URL::asset('sb_admin/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{ URL::asset('sb_admin/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ URL::asset('sb_admin/vendor/jquery-easing/jquery.easing.min.js')}}"></script>
    <script src="{{URL::asset('sb_admin/js/sb-admin-2.min.js')}}"></script>
    <!--<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>-->
    <!-- Core plugin JavaScript-->



    <!-- Custom scripts for all pages-->


    <!-- Page level plugins -->


    <!-- Page level custom scripts -->


    
    @yield("scripts")
    

</body>

</html>
