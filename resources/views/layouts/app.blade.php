<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sistema de Ventas - ALE & H')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
    

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Izquierda -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <!-- Derecha -->
        <ul class="navbar-nav ml-auto">
            <!-- Cierre de sesión -->
         <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-black" href="#" data-bs-toggle="dropdown">
                👤 {{ Auth::user()->name }}
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger">
                            Cerrar sesión
                        </button>
                    </form>
                </li>
            </ul>
        </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-text font-weight-light">Sistema de Ventas - ALE & H</span>
        </a>

        <!-- Menú -->
        <div class="sidebar">
            <nav>
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('caja.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>Caja</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('productos.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Productos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('proveedores.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-people-carry"></i>
                            <p>Proveedores</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('compras.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-cart-plus"></i>
                            <p>Compras</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('clientes.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Clientes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('ventas.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-money-bill"></i>
                            <p>Ventas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('cobros.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                            <p>Cobros</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('informes.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-newspaper"></i>
                            <p>Informes</p>
                        </a>
                    </li>

                    <!-- Agrega aquí más menús -->
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Contenido -->
    <div class="content-wrapper p-3">
        <section class="content">
            @yield('content')
        </section>
    </div>

    <!-- Footer opcional -->
</div>

<!-- Scripts -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<!-- Botones -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<!-- Bootstrap JS con Popper -->
<script src="{{ asset('adminlte/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



@stack('scripts')
@yield('scripts')
</body>
</html>
