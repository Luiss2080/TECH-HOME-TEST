<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - Tech Home Bolivia')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Evitar cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Meta tags -->
    <meta name="author" content="Desarrollado por estudiantes de la UPDS">
    <link rel="icon" href="{{ asset('faviconTH.png') }}" type="image/png">
    
    <!-- SEO Meta tags -->
    <meta name="description" content="Tech Home Bolivia: Una plataforma educativa que simula un entorno de estudio, venta de libros y herramientas, además de ofrecer cursos especializados.">
    <meta property="og:title" content="Tech Home Bolivia">

    <!-- Vite Assets -->
    @vite([
        'resources/css/app.css', 
        'resources/js/app.js'
    ])

    @stack('styles')
</head>
<body>
    <!-- Header Component -->
    @include('layouts.header')
    
    <div class="app-container">
        <!-- Sidebar Component -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="main-content-area">
            @yield('content')
            
            <!-- Footer Component -->
            @include('layouts.footer')
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/layout/sidebar.js') }}"></script>
    <script src="{{ asset('js/layout/header.js') }}"></script>
    <script src="{{ asset('js/layout/footer.js') }}"></script>
    
    @stack('scripts')
    
    <script>
        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            const userData = {
                nombre: '{{ auth()->user()->nombre ?? "" }}',
                apellido: '{{ auth()->user()->apellido ?? "" }}',
                email: '{{ auth()->user()->email ?? "" }}',
                sessionId: '{{ session()->getId() }}'
            };

            // Inicializar efectos de la aplicación
            if (typeof initAdminDashboard === 'function') {
                initAdminDashboard(userData);
            }
        });
    </script>
</body>
</html>