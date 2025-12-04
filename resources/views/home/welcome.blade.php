@extends('layouts.public')

@section('title', 'Bienvenido a TECH HOME')

@push('styles')
@vite(['resources/css/home/main.css', 'resources/css/home/components.css', 'resources/css/home/animations.css', 'resources/css/home/responsive.css'])
@endpush

@push('scripts')
@vite(['resources/js/home/main.js', 'resources/js/home/interactions.js'])
@endpush

@section('content')

<!-- Contenedor principal de la vista Home -->
<div class="crud-edit-container">
    <!-- Header TECH HOME -->
    <div class="tech-home-header">
        <div class="tech-home-logo">
            <div class="logo-icon">
                <i class="fas fa-robot"></i>
            </div>
            <h1>TECH HOME</h1>
            <span style="font-size: 0.9rem; color: #6b7280; margin-left: 10px;">Instituto de Robótica</span>
        </div>
        <div class="header-info">
            <div class="sistema-online">
                <i class="fas fa-wifi"></i>
                <span>Sistema Online</span>
            </div>
            <div class="digital-clock" id="techHomeTime">
                <!-- Se actualiza con JavaScript -->
            </div>
            @auth
                <div style="font-size: 0.9rem; color: #6b7280;">{{ date('d/m/Y') }}</div>
                <button onclick="window.location.href='#'" style="background: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">Perfil</button>
            @else
                <div style="font-size: 0.9rem; color: #6b7280;">{{ date('d/m/Y') }}</div>
                <button onclick="window.location.href='#'" style="background: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">Iniciar Sesión</button>
            @endauth
        </div>
    </div>

    <!-- Hero Section -->
    <div class="tech-home-hero">
        <div class="hero-content">
            <div class="hero-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="hero-text">
                <h2>Bienvenido al Instituto de Robótica</h2>
                <h3>TECH HOME</h3>
                <p>
                    @auth
                        Hola {{ auth()->user()->nombre ?? 'Usuario' }}, portal de acceso al ecosistema tecnológico más avanzado
                    @else
                        Portal de acceso al ecosistema tecnológico más avanzado
                    @endauth
                </p>
            </div>
            <div class="sistema-status">
                <div class="sistema-online">
                    <span>23:10:01</span>
                </div>
                <div style="font-size: 0.8rem; color: #6b7280; text-align: center;">
                    Miércoles, 4 De Diciembre De 2025
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Explorar TECH HOME -->
    <div class="crud-section-header">
        <h3>
            <div class="section-icon">
                <i class="fas fa-compass"></i>
            </div>
            Explora el Ecosistema TECH HOME
        </h3>
        <p>Descubre las áreas principales de nuestro instituto de robótica</p>
    </div>
    
    <div class="tech-home-quick-actions">
        <div class="quick-action-card">
            <div class="quick-action-icon biblioteca">
                <i class="fas fa-book"></i>
            </div>
            <div class="quick-action-title">Biblioteca Digital</div>
            <div class="quick-action-description">Recursos académicos especializados</div>
            <div class="quick-action-stats">
                <div class="stat-number">{{ $estadisticas['libros'] ?? '2' }}</div>
                <div class="stat-label">libros disponibles</div>
            </div>
            <button class="quick-action-button">
                <i class="fas fa-arrow-right"></i>
                Explorar
            </button>
        </div>

        <div class="quick-action-card">
            <div class="quick-action-icon componentes">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="quick-action-title">Centro de Componentes</div>
            <div class="quick-action-description">Hardware y tecnología robótica</div>
            <div class="quick-action-stats">
                <div class="stat-number">{{ $estadisticas['componentes'] ?? '1' }}</div>
                <div class="stat-label">componentes activos</div>
            </div>
            <button class="quick-action-button">
                <i class="fas fa-arrow-right"></i>
                Consultar
            </button>
        </div>

        <div class="quick-action-card">
            <div class="quick-action-icon cursos">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="quick-action-title">Cursos Especializados</div>
            <div class="quick-action-description">Formación en robótica e IA</div>
            <div class="quick-action-stats">
                <div class="stat-number">{{ $estadisticas['cursos'] ?? '45' }}</div>
                <div class="stat-label">cursos activos</div>
            </div>
            <button class="quick-action-button">
                <i class="fas fa-arrow-right"></i>
                Inscribirse
            </button>
        </div>

        <div class="quick-action-card">
            <div class="quick-action-icon proyectos">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="quick-action-title">Proyectos Innovadores</div>
            <div class="quick-action-description">Desarrollos tecnológicos en curso</div>
            <div class="quick-action-stats">
                <div class="stat-number">{{ $estadisticas['proyectos'] ?? '128' }}</div>
                <div class="stat-label">en desarrollo</div>
            </div>
            <button class="quick-action-button">
                <i class="fas fa-arrow-right"></i>
                Ver Curso
            </button>
        </div>
    </div>

    <!-- Sección: Estadísticas del Sistema -->
    <div class="crud-section-header">
        <h3>
            <div class="section-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            Estado del Sistema
        </h3>
        <p>Monitoreo en tiempo real del ecosistema TECH HOME</p>
    </div>
    
    <div class="crud-info-panel">
        <div class="system-tabs">
            <button class="tab-button active" data-tab="general">
                <i class="fas fa-chart-pie"></i>
                General
            </button>
            <button class="tab-button" data-tab="rendimiento">
                <i class="fas fa-tachometer-alt"></i>
                Rendimiento
            </button>
            <button class="tab-button" data-tab="actividad">
                <i class="fas fa-activity"></i>
                Actividad
            </button>
        </div>
        
        <div class="tab-content active" data-tab-content="general">
            <div class="system-status-grid">
                <div class="status-card">
                    <div class="status-number">{{ $estadisticas['libros'] ?? '2' }}</div>
                    <div class="status-label">Libros Especializados</div>
                    <div class="status-description">en nuestro catálogo</div>
                    <div class="status-indicator active">disponibles</div>
                </div>

                <div class="status-card">
                    <div class="status-number">{{ $estadisticas['componentes'] ?? '1' }}</div>
                    <div class="status-label">Componentes Disponibles</div>
                    <div class="status-description">en nuestro inventario</div>
                    <div class="status-indicator available">en stock</div>
                </div>

                <div class="status-card">
                    <div class="status-number">{{ $estadisticas['cursos'] ?? '45' }}</div>
                    <div class="status-label">Cursos Activos</div>
                    <div class="status-description">para inscribirse</div>
                    <div class="status-indicator progress">en proceso</div>
                </div>
            </div>
        </div>
        
        <div class="tab-content" data-tab-content="rendimiento">
            <div class="system-status-grid">
                <div class="status-card">
                    <div class="status-number">85ms</div>
                    <div class="status-label">Tiempo de Respuesta</div>
                    <div class="status-description">del sistema</div>
                    <div class="status-indicator active">excelente</div>
                </div>

                <div class="status-card">
                    <div class="status-number">99.8%</div>
                    <div class="status-label">Disponibilidad</div>
                    <div class="status-description">de servidores</div>
                    <div class="status-indicator active">óptimo</div>
                </div>

                <div class="status-card">
                    <div class="status-number">156</div>
                    <div class="status-label">Conexiones Activas</div>
                    <div class="status-description">de 230 máx.</div>
                    <div class="status-indicator available">normal</div>
                </div>
            </div>
        </div>
        
        <div class="tab-content" data-tab-content="actividad">
            <div class="system-status-grid">
                <div class="status-card">
                    <div class="status-number">Online</div>
                    <div class="status-label">Sistema Operativo</div>
                    <div class="status-description">funcionando correctamente</div>
                    <div class="status-indicator active">hace 2 minutos</div>
                </div>

                <div class="status-card">
                    <div class="status-number">Nuevo</div>
                    <div class="status-label">Usuario Registrado</div>
                    <div class="status-description">estudiante agregado</div>
                    <div class="status-indicator available">hace 15 minutos</div>
                </div>

                <div class="status-card">
                    <div class="status-number">2:00 AM</div>
                    <div class="status-label">Mantenimiento</div>
                    <div class="status-description">programado</div>
                    <div class="status-indicator progress">en proceso</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Biblioteca Digital Especializada -->
    <div class="crud-section-header">
        <h3>
            <div class="section-icon">
                <i class="fas fa-book-open"></i>
            </div>
            Biblioteca Digital Especializada
        </h3>
        <p>Accede a recursos académicos de vanguardia en robótica e inteligencia artificial</p>
    </div>
    
    <div class="digital-library-grid">
        <div class="library-card robotica">
            <div class="library-icon robotica">
                <i class="fas fa-robot"></i>
            </div>
            <div class="library-title">Robótica Avanzada</div>
            <div class="library-description">Libros especializados en robótica móvil y manipuladores</div>
            <button class="library-button catalog">Ver Catálogo</button>
        </div>
        
        <div class="library-card ia">
            <div class="library-icon ia">
                <i class="fas fa-brain"></i>
            </div>
            <div class="library-title">Inteligencia Artificial</div>
            <div class="library-description">Recursos sobre machine learning y deep learning</div>
            <button class="library-button explore">Explorar</button>
        </div>
        
        <div class="library-card programacion">
            <div class="library-icon programacion">
                <i class="fas fa-code"></i>
            </div>
            <div class="library-title">Programación Avanzada</div>
            <div class="library-description">Lenguajes especializados para sistemas embebidos</div>
            <button class="library-button consult">Consultar</button>
        </div>
        
        <div class="library-card ingenieria">
            <div class="library-icon ingenieria">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="library-title">Ingeniería de Control</div>
            <div class="library-description">Sistemas de control automático y teoría</div>
            <button class="library-button access">Acceder</button>
        </div>
    </div>

    <!-- Sección: Centro de Componentes Electrónicos -->
    <div class="crud-section-header">
        <h3>
            <div class="section-icon">
                <i class="fas fa-microchip"></i>
            </div>
            Centro de Componentes Electrónicos
        </h3>
        <p>Inventario completo de hardware especializado para proyectos robóticos</p>
    </div>
    
    <div class="components-grid">
        <div class="component-card microcontroladores">
            <div class="component-icon">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="component-title">Microcontroladores</div>
            <div class="component-description">Arduino, Raspberry Pi y sistemas embebidos</div>
            <button class="component-button">Ver Stock</button>
        </div>
        
        <div class="component-card sensores">
            <div class="component-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="component-title">Sensores Avanzados</div>
            <div class="component-description">Cámaras, LIDAR, ultrasónicos y táctiles</div>
            <button class="component-button">Explorar</button>
        </div>
        
        <div class="component-card actuadores">
            <div class="component-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="component-title">Actuadores y Motores</div>
            <div class="component-description">Servos, motores paso a paso y lineales</div>
            <button class="component-button">Consultar</button>
        </div>
        
        <div class="component-card fuentes">
            <div class="component-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="component-title">Fuentes y Baterías</div>
            <div class="component-description">Sistemas de alimentación especializados</div>
            <button class="component-button">Ver Opciones</button>
        </div>
    </div>

    <!-- Espacio de separación -->
    <div style="height: 20px;"></div>

</div>

@endsection