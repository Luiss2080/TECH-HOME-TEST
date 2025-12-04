<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="TECH HOME - Instituto de Robótica. Cursos profesionales de robótica, programación y tecnología. Aprende con los mejores profesionales del sector.">
    <meta name="keywords" content="robótica, programación, tecnología, cursos, instituto, TECH HOME">
    <meta name="author" content="TECH HOME Instituto de Robótica">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('title', 'TECH HOME - Instituto de Robótica')">
    <meta property="og:description" content="Instituto líder en formación de robótica y tecnología. Cursos profesionales y certificaciones.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/tech-home-logo.png') }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'TECH HOME - Instituto de Robótica')">
    <meta name="twitter:description" content="Instituto líder en formación de robótica y tecnología.">
    
    <title>@yield('title', 'TECH HOME - Instituto de Robótica')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    @stack('styles')
    
    <!-- Custom CSS Variables -->
    <style>
        :root {
            --primary-red: #dc2626;
            --primary-red-hover: #b91c1c;
            --primary-red-light: #fecaca;
            --primary-red-dark: #7f1d1d;
            
            --secondary-blue: #1e40af;
            --secondary-blue-hover: #1d4ed8;
            --secondary-blue-light: #dbeafe;
            
            --accent-orange: #f59e0b;
            --accent-green: #10b981;
            
            --neutral-white: #ffffff;
            --neutral-gray-50: #f9fafb;
            --neutral-gray-100: #f3f4f6;
            --neutral-gray-200: #e5e7eb;
            --neutral-gray-300: #d1d5db;
            --neutral-gray-400: #9ca3af;
            --neutral-gray-500: #6b7280;
            --neutral-gray-600: #4b5563;
            --neutral-gray-700: #374151;
            --neutral-gray-800: #1f2937;
            --neutral-gray-900: #111827;
            
            --font-primary: 'Inter', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        
        /* Estilos base para páginas públicas */
        body {
            font-family: var(--font-primary);
            background: linear-gradient(135deg, var(--neutral-gray-50) 0%, var(--neutral-white) 100%);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: var(--neutral-gray-800);
        }
        
        /* Container para páginas públicas */
        .public-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header público simple */
        .public-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--neutral-gray-200);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .public-header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }
        
        .public-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.25rem;
        }
        
        .public-logo i {
            font-size: 2rem;
        }
        
        .public-auth-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .public-auth-buttons a {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .public-login-btn {
            color: var(--primary-red);
            border: 2px solid var(--primary-red);
            background: transparent;
        }
        
        .public-login-btn:hover {
            background: var(--primary-red);
            color: white;
            transform: translateY(-2px);
        }
        
        .public-register-btn {
            background: var(--primary-red);
            color: white;
            border: 2px solid var(--primary-red);
        }
        
        .public-register-btn:hover {
            background: var(--primary-red-hover);
            border-color: var(--primary-red-hover);
            transform: translateY(-2px);
        }
        
        /* Main content */
        .public-main {
            flex: 1;
            width: 100%;
        }
        
        /* Footer público */
        .public-footer {
            background: var(--neutral-gray-900);
            color: var(--neutral-gray-300);
            padding: 2rem 0;
            margin-top: auto;
        }
        
        .public-footer-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            padding: 0 2rem;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .public-header-content {
                padding: 0 1rem;
            }
            
            .public-auth-buttons {
                gap: 0.5rem;
            }
            
            .public-auth-buttons a {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            .public-logo {
                font-size: 1.125rem;
            }
            
            .public-logo i {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Container principal para páginas públicas -->
    <div class="public-container">
        
        <!-- Header público (opcional, solo si se desea mostrar) -->
        @if(!isset($hideHeader) || !$hideHeader)
            <header class="public-header">
                <div class="public-header-content">
                    <a href="{{ route('home') }}" class="public-logo">
                        <i class="fas fa-robot"></i>
                        <div>
                            <strong>TECH HOME</strong>
                            <small style="display: block; font-size: 0.75rem; font-weight: 400; color: var(--neutral-gray-600);">
                                Instituto de Robótica
                            </small>
                        </div>
                    </a>
                    
                    <div class="public-auth-buttons">
                        <a href="{{ route('auth.login') }}" class="public-login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Iniciar Sesión</span>
                        </a>
                        <a href="{{ route('auth.register') }}" class="public-register-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Registrarse</span>
                        </a>
                    </div>
                </div>
            </header>
        @endif
        
        <!-- Contenido principal -->
        <main class="public-main">
            @yield('content')
        </main>
        
        <!-- Footer público (opcional) -->
        @if(!isset($hideFooter) || !$hideFooter)
            <footer class="public-footer">
                <div class="public-footer-content">
                    <p>&copy; {{ date('Y') }} TECH HOME - Instituto de Robótica. Todos los derechos reservados.</p>
                    <p style="font-size: 0.875rem; color: var(--neutral-gray-500); margin-top: 0.5rem;">
                        Formando los profesionales del futuro en robótica y tecnología
                    </p>
                </div>
            </footer>
        @endif
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')
    
    <!-- Scripts específicos para páginas públicas -->
    <script>
        // Efectos básicos para páginas públicas
        document.addEventListener('DOMContentLoaded', function() {
            // Animación suave para botones
            const buttons = document.querySelectorAll('.public-auth-buttons a');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.boxShadow = 'var(--shadow-lg)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.boxShadow = 'var(--shadow-md)';
                });
            });
            
            // Log para debug
            console.log('✅ TECH HOME - Layout público inicializado');
        });
    </script>
</body>
</html>