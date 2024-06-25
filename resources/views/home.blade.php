<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Hotel Project')</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/jpg">
    @vite('resources/css/app.css')
    @vite('resources/css/sidebar.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" />
    
</head>
<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> 
            <i class='bx bx-menu' id="header-toggle"></i> 
        </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="#" class="nav_logo"> 
                    <i class='fas fa-user-tie nav_logo-icon'></i> 
                    <span class="nav_logo-name">Administrador</span>
                </a>
                <hr class="sidebar-hr" style="border-top: 2px solid white;">
                <div class="nav_list"> 
                    <a href="{{ route('dashboard') }}" class="nav_link {{ request()->routeIs('dashboard') ? 'active' : '' }}"> 
                        <i class="fas fa-home nav_icon"></i> 
                        <span class="nav_name">Dashboard</span> 
                    </a> 
                    <a href="{{ route('users') }}" class="nav_link {{ request()->routeIs('users') ? 'active' : '' }}"> 
                        <i class='fas fa-users nav_icon'></i> 
                        <span class="nav_name">Usuarios</span> 
                    </a>  
                    <a href="#" class="nav_link report-toggle"> 
                        <i class='fas fa-chart-pie nav_icon'></i> 
                        <span class="nav_name">Reportes</span>
                        <i class='fas fa-chevron-right nav_icon arrow'></i> 
                    </a>
                    <div class="report-submenu">
                        <a href="{{ route('reporte1') }}" class="nav_link sub {{ request()->routeIs('reporte1') ? 'active' : '' }}"> 
                            <i class='fas fa-clipboard nav_icon'></i> 
                            <span class="nav_name">Reporte 1</span> 
                        </a>
                        <a href="{{ route('reporte2') }}" class="nav_link sub {{ request()->routeIs('reporte2') ? 'active' : '' }}"> 
                            <i class='fas fa-clipboard nav_icon'></i> 
                            <span class="nav_name">Reporte 2</span> 
                        </a>
                        <a href="#" class="nav_link sub"> 
                            <i class='fas fa-clipboard nav_icon'></i> 
                            <span class="nav_name">Reporte 3</span> 
                        </a>
                        <a href="#" class="nav_link sub"> 
                            <i class='fas fa-clipboard nav_icon'></i> 
                            <span class="nav_name">Reporte 4</span> 
                        </a>
                    </div>
                    <a href="{{ route('habitaciones') }}" class="nav_link {{ request()->routeIs('habitaciones') ? 'active' : '' }}"> 
                        <i class='fas fa-bed nav_icon'></i> 
                        <span class="nav_name">Habitaciones</span> 
                    </a> 
                    <a href="{{ route('configuracion') }}" class="nav_link {{ request()->routeIs('configuracion') ? 'active' : '' }}"> 
                        <i class='fas fa-cog nav_icon'></i> 
                        <span class="nav_name">Configuración</span> 
                    </a> 
                </div>
            </div> 
        </nav>
        <div class="nav_logout_container">
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav_logo btn-logout">
                    <i class='fas fa-sign-out nav_logo-icon-abajo'></i>
                    <span class="nav_logo-abajo">Logout</span>
                </button>
            </form>
        </div>
    </div>
    <div class="height-100" style="background-color: #EEEEEE">
        @yield('content')
    </div>

    @vite('resources/js/sidebar.js')

</body>
</html>
