@extends('layouts.app')

@section('title', 'TECH HOME - Instituto de Robótica')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/welcome.css') }}">
@endpush

@section('content')
<div class="welcome-dashboard">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <div class="breadcrumb-container">
            <i class="fas fa-home breadcrumb-icon"></i>
            <span class="breadcrumb-text">Inicio</span>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="hero-icon">
                <div class="icon-container">
                    <i class="fas fa-robot"></i>
                </div>
            </div>
            <div class="hero-text">
                <h1 class="hero-title">Bienvenido al Instituto de Robótica</h1>
                <h2 class="hero-subtitle">TECH HOME</h2>
                <p class="hero-description">Portal de acceso al ecosistema tecnológico más avanzado</p>
            </div>
            <div class="hero-status">
                <div class="status-indicator online">
                    <i class="fas fa-circle"></i>
                    <span>Sistema Online</span>
                </div>
                <div class="current-datetime">
                    <div class="current-time" id="welcome-current-time">11:21:50</div>
                    <div class="current-date">Jueves, 4 de Diciembre de 2025</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard -->
    <div class="dashboard-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-th-large"></i>
            </div>
            <h3 class="section-title">Explora el Ecosistema TECH HOME</h3>
        </div>

        <div class="dashboard-grid">
            <!-- Biblioteca Digital -->
            <div class="dashboard-card" data-module="biblioteca">
                <div class="card-header">
                    <div class="card-icon biblioteca">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="card-info">
                        <h4 class="card-title">Biblioteca Digital</h4>
                        <p class="card-description">Recursos académicos especializados</p>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-number" data-target="2">0</span>
                        <span class="stat-label">libros disponibles</span>
                    </div>
                </div>
                <div class="card-overlay">
                    <div class="overlay-content">
                        <i class="fas fa-book-open"></i>
                        <span>Acceder a Biblioteca</span>
                    </div>
                </div>
            </div>

            <!-- Centro de Componentes -->
            <div class="dashboard-card" data-module="componentes">
                <div class="card-header">
                    <div class="card-icon componentes">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="card-info">
                        <h4 class="card-title">Centro de Componentes</h4>
                        <p class="card-description">Hardware y tecnología robótica</p>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-number" data-target="{{ $componentesActivos ?? 1 }}">0</span>
                        <span class="stat-label">componentes activos</span>
                    </div>
                </div>
                <div class="card-overlay">
                    <div class="overlay-content">
                        <i class="fas fa-cogs"></i>
                        <span>Explorar Componentes</span>
                    </div>
                </div>
            </div>

            <!-- Cursos Especializados -->
            <div class="dashboard-card" data-module="cursos">
                <div class="card-header">
                    <div class="card-icon cursos">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="card-info">
                        <h4 class="card-title">Cursos Especializados</h4>
                        <p class="card-description">Formación en robótica e IA</p>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-number" data-target="{{ $cursosActivos ?? 45 }}">0</span>
                        <span class="stat-label">cursos activos</span>
                    </div>
                </div>
                <div class="card-overlay">
                    <div class="overlay-content">
                        <i class="fas fa-play-circle"></i>
                        <span>Iniciar Aprendizaje</span>
                    </div>
                </div>
            </div>

            <!-- Proyectos Innovadores -->
            <div class="dashboard-card" data-module="proyectos">
                <div class="card-header">
                    <div class="card-icon proyectos">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div class="card-info">
                        <h4 class="card-title">Proyectos Innovadores</h4>
                        <p class="card-description">Desarrollos tecnológicos en curso</p>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-number" data-target="{{ $proyectosDesarrollo ?? 128 }}">0</span>
                        <span class="stat-label">en desarrollo</span>
                    </div>
                </div>
                <div class="card-overlay">
                    <div class="overlay-content">
                        <i class="fas fa-lightbulb"></i>
                        <span>Ver Proyectos</span>
                    </div>
                </div>
            </div>

            <!-- Laboratorios -->
            <div class="dashboard-card" data-module="laboratorios">
                <div class="card-header">
                    <div class="card-icon laboratorios">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="card-info">
                        <h4 class="card-title">Laboratorios</h4>
                        <p class="card-description">Espacios de experimentación</p>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-number" data-target="{{ $laboratoriosDisponibles ?? 8 }}">0</span>
                        <span class="stat-label">disponibles</span>
                    </div>
                </div>
                <div class="card-overlay">
                    <div class="overlay-content">
                        <i class="fas fa-microscope"></i>
                        <span>Acceder a Labs</span>
                    </div>
                </div>
            </div>

            <!-- Estudiantes -->
            <div class="dashboard-card" data-module="estudiantes">
                <div class="card-header">
                    <div class="card-icon estudiantes">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-info">
                        <h4 class="card-title">Comunidad</h4>
                        <p class="card-description">Estudiantes y profesionales</p>
                    </div>
                    <div class="card-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-number" data-target="{{ $estudiantesActivos ?? 1250 }}">0</span>
                        <span class="stat-label">miembros activos</span>
                    </div>
                </div>
                <div class="card-overlay">
                    <div class="overlay-content">
                        <i class="fas fa-user-friends"></i>
                        <span>Ver Comunidad</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-section">
        <div class="section-header">
            <div class="section-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <h3 class="section-title">Acciones Rápidas</h3>
        </div>

        <div class="quick-actions-grid">
            <a href="{{ route('auth.login') }}" class="quick-action-item">
                <div class="action-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <span class="action-label">Iniciar Sesión</span>
            </a>

            <a href="{{ route('auth.register') }}" class="quick-action-item">
                <div class="action-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <span class="action-label">Registrarse</span>
            </a>

            <a href="#" class="quick-action-item">
                <div class="action-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <span class="action-label">Más Info</span>
            </a>

            <a href="#" class="quick-action-item">
                <div class="action-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <span class="action-label">Contacto</span>
            </a>
        </div>
    </div>

    <!-- Version Info -->
    <div class="version-section">
        <div class="version-badge">
            <span class="version-label">Versión 2.0</span>
        </div>
        <div class="build-info">
            <span class="build-number">Build #2025.1</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/welcome.js') }}" defer></script>
@endpush
