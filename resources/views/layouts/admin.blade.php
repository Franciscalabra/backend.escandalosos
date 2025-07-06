<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin') - Pizzería Escandalosos</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 60px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: #212529;
            padding-top: var(--header-height);
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 12px 20px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--header-height);
            min-height: 100vh;
        }
        
        .top-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }
        
        .content-wrapper {
            padding: 30px;
        }
        
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,.08);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .table-responsive {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,.08);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .top-header {
                left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="text-center mb-4">
            <h5 class="text-white">🍕 Pizzería Admin</h5>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" 
                   href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-shopping-cart"></i> Órdenes
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" 
                   href="{{ route('admin.categories.index') }}">
                    <i class="fas fa-folder"></i> Categorías
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" 
                   href="{{ route('admin.products.index') }}">
                    <i class="fas fa-pizza-slice"></i> Productos
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.ingredients.*') ? 'active' : '' }}" 
                   href="{{ route('admin.ingredients.index') }}">
                    <i class="fas fa-cheese"></i> Ingredientes
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.combo-rules.*') ? 'active' : '' }}" 
                   href="{{ route('admin.combo-rules.index') }}">
                    <i class="fas fa-percentage"></i> Reglas de Combos
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.delivery-zones.*') ? 'active' : '' }}" 
                   href="{{ route('admin.delivery-zones.index') }}">
                    <i class="fas fa-map-marked-alt"></i> Zonas de Delivery
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                   href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
            </li>
            
            <li class="nav-item mt-3">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-danger">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </button>
                </form>
            </li>
        </ul>
    </nav>
    
    <!-- Header -->
    <header class="top-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h4 class="mb-0">@yield('header', 'Dashboard')</h4>
            
            <div class="d-flex align-items-center">
                <span class="me-3">Hola, {{ Auth::guard('admin')->user()->name }}</span>
                <button class="btn btn-sm btn-outline-secondary" onclick="location.href='/'">
                    <i class="fas fa-external-link-alt"></i> Ver Sitio
                </button>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>