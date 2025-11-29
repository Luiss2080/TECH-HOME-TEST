@extends('layouts.app')

@section('title', 'Bienvenido - Tech Home')

@section('styles')
    @vite(['resources/css/modulos/home/welcome.css'])
@endsection

@section('content')
<!-- Contenedor principal de la vista Home -->
<div class="crud-edit-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal de bienvenida -->
        <div class="crud-section-card tech-home-hero">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon tech-home-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <nav aria-label="breadcrumb" class="crud-breadcrumb-nav">
                            <ol class="crud-breadcrumb">
                                <li class="crud-breadcrumb-item active">
                                    <i class="fas fa-home"></i>
                                    Inicio
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title tech-home-title">
                            Bienvenido al Instituto de Robótica
                            <span class="tech-home-brand">TECH HOME</span>
                        </h1>
                        <p class="crud-section-subtitle tech-home-subtitle">
                            @auth
                                Hola {{ Auth::user()->name }}, estás conectado al futuro de la robótica y la tecnología
                            @else
                                Portal de acceso al ecosistema tecnológico más avanzado
                            @endauth
                        </p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <div class="tech-home-status">
                        <div class="status-indicator online">
                            <i class="fas fa-wifi"></i>
                            <span>Sistema Online</span>
                        </div>
                        <div class="current-time" id="techHomeTime">
                            <!-- Se actualiza con JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Elementos decorativos robóticos -->
            <div class="tech-home-decorations">
                <div class="floating-robot robot-1">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="floating-robot robot-2">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="floating-robot robot-3">
                    <i class="fas fa-microchip"></i>
                </div>
                <div class="circuit-lines">
                    <div class="circuit-line line-1"></div>
                    <div class="circuit-line line-2"></div>
                    <div class="circuit-line line-3"></div>
                </div>
            </div>
        </div>

        <!-- Sección: Explorar TECH HOME -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-compass"></i>
                    Explora el Ecosistema TECH HOME
                </h2>
                <p class="crud-section-subtitle">Descubre las áreas principales de nuestro instituto de robótica</p>
            </div>
            
            <div class="crud-form-body">
                <div class="tech-home-quick-actions">
                    <div class="quick-action-card" onclick="showInfo('biblioteca')">
                        <div class="quick-action-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="quick-action-content">
                            <h4>Biblioteca Digital</h4>
                            <p>Recursos académicos especializados</p>
                            <div class="quick-action-stats">
                                <span class="stat-number">{{ $estadisticas['libros'] ?? '2,847' }}</span>
                                <span class="stat-label">libros disponibles</span>
                            </div>
                        </div>
                        <div class="quick-action-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                    <div class="quick-action-card" onclick="showInfo('componentes')">
                        <div class="quick-action-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="quick-action-content">
                            <h4>Centro de Componentes</h4>
                            <p>Hardware y tecnología robótica</p>
                            <div class="quick-action-stats">
                                <span class="stat-number">{{ $estadisticas['componentes'] ?? '1,523' }}</span>
                                <span class="stat-label">componentes activos</span>
                            </div>
                        </div>
                        <div class="quick-action-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                    <div class="quick-action-card" onclick="showInfo('cursos')">
                        <div class="quick-action-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="quick-action-content">
                            <h4>Cursos Especializados</h4>
                            <p>Formación en robótica e IA</p>
                            <div class="quick-action-stats">
                                <span class="stat-number">{{ $estadisticas['cursos'] ?? '45' }}</span>
                                <span class="stat-label">cursos activos</span>
                            </div>
                        </div>
                        <div class="quick-action-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                    <div class="quick-action-card" onclick="showInfo('proyectos')">
                        <div class="quick-action-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="quick-action-content">
                            <h4>Proyectos Innovadores</h4>
                            <p>Desarrollos tecnológicos en curso</p>
                            <div class="quick-action-stats">
                                <span class="stat-number">{{ $estadisticas['proyectos'] ?? '128' }}</span>
                                <span class="stat-label">en desarrollo</span>
                            </div>
                        </div>
                        <div class="quick-action-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Estadísticas del Sistema -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-chart-line"></i>
                    Estado del Sistema
                </h2>
                <p class="crud-section-subtitle">Monitoreo en tiempo real del ecosistema TECH HOME</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-info-panel">
                    <div class="crud-info-tabs">
                        <button class="crud-info-tab active" data-tab="general">
                            <i class="fas fa-chart-pie"></i>
                            General
                        </button>
                        <button class="crud-info-tab" data-tab="rendimiento">
                            <i class="fas fa-tachometer-alt"></i>
                            Rendimiento
                        </button>
                        <button class="crud-info-tab" data-tab="actividad">
                            <i class="fas fa-activity"></i>
                            Actividad
                        </button>
                    </div>
                    
                    <div class="crud-info-pane active" id="general">
                        <div class="tech-home-stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">{{ $estadisticas['libros_total'] ?? '2,847' }}</div>
                                    <div class="stat-label">Libros Especializados</div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-plus"></i>
                                        +{{ $estadisticas['libros_nuevos'] ?? '15' }} este mes
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-microchip"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">{{ $estadisticas['componentes'] ?? '1,523' }}</div>
                                    <div class="stat-label">Componentes Disponibles</div>
                                    <div class="stat-change warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $estadisticas['stock_bajo'] ?? '12' }} stock bajo
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">{{ $estadisticas['cursos'] ?? '45' }}</div>
                                    <div class="stat-label">Cursos Activos</div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-users"></i>
                                        {{ $estadisticas['estudiantes_inscritos'] ?? '342' }} inscritos
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="rendimiento">
                        <div class="performance-metrics">
                            <div class="metric-item">
                                <div class="metric-label">Tiempo de Respuesta del Sistema</div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 85%;"></div>
                                </div>
                                <div class="metric-value">85ms - Excelente</div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-label">Disponibilidad de Servidores</div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 99%;"></div>
                                </div>
                                <div class="metric-value">99.8% - Óptimo</div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-label">Conexiones Activas</div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 67%;"></div>
                                </div>
                                <div class="metric-value">156/230 - Normal</div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-label">Uso de Recursos</div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 45%;"></div>
                                </div>
                                <div class="metric-value">45% - Óptimo</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="actividad">
                        <div class="activity-feed">
                            @if (!empty($actividades_recientes))
                                @foreach (array_slice($actividades_recientes, 0, 5) as $actividad)
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-{{ ($actividad['tipo'] ?? '') === 'usuario' ? 'user' : (($actividad['tipo'] ?? '') === 'sistema' ? 'cog' : 'info-circle') }}"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">{{ $actividad['titulo'] ?? 'Actividad del sistema' }}</div>
                                            <div class="activity-description">{{ $actividad['descripcion'] ?? 'Sin descripción' }}</div>
                                            <div class="activity-time">{{ $actividad['tiempo'] ?? 'Hace un momento' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Sistema iniciado correctamente</div>
                                        <div class="activity-description">Todos los módulos robóticos están operativos</div>
                                        <div class="activity-time">Hace 2 minutos</div>
                                    </div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Nuevo usuario registrado</div>
                                        <div class="activity-description">Usuario estudiante agregado al sistema</div>
                                        <div class="activity-time">Hace 15 minutos</div>
                                    </div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-microchip"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Inventario actualizado</div>
                                        <div class="activity-description">Nuevos sensores Arduino agregados al stock</div>
                                        <div class="activity-time">Hace 1 hora</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Biblioteca Digital -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-book-open"></i>
                    Biblioteca Digital Especializada
                </h2>
                <p class="crud-section-subtitle">Accede a recursos académicos de vanguardia en robótica e inteligencia artificial</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Robótica Avanzada</h4>
                            <p>Libros especializados en robótica móvil y manipuladores</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('robotica-libros')">
                                <i class="fas fa-book"></i>
                                Ver Catálogo
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            845 títulos
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Inteligencia Artificial</h4>
                            <p>Recursos sobre machine learning y deep learning</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('ia-libros')">
                                <i class="fas fa-brain"></i>
                                Explorar
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            632 títulos
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Programación Avanzada</h4>
                            <p>Lenguajes especializados para sistemas embebidos</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('programacion-libros')">
                                <i class="fas fa-code"></i>
                                Consultar
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            589 títulos
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Ingeniería de Control</h4>
                            <p>Sistemas de control automático y teoría</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('control-libros')">
                                <i class="fas fa-cogs"></i>
                                Acceder
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            781 títulos
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Centro de Componentes -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-microchip"></i>
                    Centro de Componentes Electrónicos
                </h2>
                <p class="crud-section-subtitle">Inventario completo de hardware especializado para proyectos robóticos</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-comp-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Microcontroladores</h4>
                            <p>Arduino, Raspberry Pi y sistemas embebidos</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('microcontroladores')">
                                <i class="fas fa-cpu"></i>
                                Ver Stock
                            </button>
                        </div>
                        <div class="comp-status available">
                            <i class="fas fa-circle"></i>
                            Disponible
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-comp-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Sensores Avanzados</h4>
                            <p>Cámaras, LIDAR, ultrasónicos y táctiles</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('sensores')">
                                <i class="fas fa-search"></i>
                                Explorar
                            </button>
                        </div>
                        <div class="comp-status available">
                            <i class="fas fa-circle"></i>
                            Disponible
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-comp-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Actuadores y Motores</h4>
                            <p>Servos, motores paso a paso y lineales</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('actuadores')">
                                <i class="fas fa-play"></i>
                                Consultar
                            </button>
                        </div>
                        <div class="comp-status low-stock">
                            <i class="fas fa-circle"></i>
                            Stock Bajo
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-comp-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Fuentes y Baterías</h4>
                            <p>Sistemas de alimentación especializados</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('alimentacion')">
                                <i class="fas fa-battery-full"></i>
                                Ver Opciones
                            </button>
                        </div>
                        <div class="comp-status available">
                            <i class="fas fa-circle"></i>
                            Disponible
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Cursos Especializados -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-graduation-cap"></i>
                    Cursos de Robótica e IA
                </h2>
                <p class="crud-section-subtitle">Formación especializada desde nivel básico hasta investigación avanzada</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-course-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Robótica Básica</h4>
                            <p>Introducción a la programación de robots</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('robotica-basica')">
                                <i class="fas fa-play"></i>
                                Comenzar
                            </button>
                        </div>
                        <div class="course-info">
                            <div class="course-level beginner">Básico</div>
                            <div class="course-duration">40 horas</div>
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-course-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Machine Learning</h4>
                            <p>Algoritmos de aprendizaje automático aplicado</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('machine-learning')">
                                <i class="fas fa-rocket"></i>
                                Inscribirse
                            </button>
                        </div>
                        <div class="course-info">
                            <div class="course-level intermediate">Intermedio</div>
                            <div class="course-duration">60 horas</div>
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-course-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Visión Artificial</h4>
                            <p>Procesamiento de imágenes y reconocimiento</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('vision-artificial')">
                                <i class="fas fa-camera"></i>
                                Ver Curso
                            </button>
                        </div>
                        <div class="course-info">
                            <div class="course-level advanced">Avanzado</div>
                            <div class="course-duration">80 horas</div>
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-course-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>IoT y Automatización</h4>
                            <p>Internet de las cosas y sistemas conectados</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('iot-automatizacion')">
                                <i class="fas fa-link"></i>
                                Explorar
                            </button>
                        </div>
                        <div class="course-info">
                            <div class="course-level intermediate">Intermedio</div>
                            <div class="course-duration">50 horas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-rocket"></i>
                    Laboratorios Virtuales
                </h2>
                <p class="crud-section-subtitle">Accede a las herramientas de desarrollo y simulación</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-lab-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>IDE de Programación</h4>
                            <p>Entorno integrado para desarrollo robótico</p>
                            <button type="button" class="crud-btn-action" onclick="openLab('programming')">
                                <i class="fas fa-play"></i>
                                Iniciar
                            </button>
                        </div>
                        <div class="lab-status online">
                            <i class="fas fa-circle"></i>
                            Online
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lab-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-cube"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Simulador 3D</h4>
                            <p>Pruebas virtuales de prototipos robóticos</p>
                            <button type="button" class="crud-btn-action" onclick="openLab('simulator')">
                                <i class="fas fa-cube"></i>
                                Simular
                            </button>
                        </div>
                        <div class="lab-status online">
                            <i class="fas fa-circle"></i>
                            Online
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lab-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>IA Training Hub</h4>
                            <p>Entrenamiento de modelos de inteligencia artificial</p>
                            <button type="button" class="crud-btn-action" onclick="openLab('ai')">
                                <i class="fas fa-brain"></i>
                                Entrenar
                            </button>
                        </div>
                        <div class="lab-status maintenance">
                            <i class="fas fa-circle"></i>
                            Mantenimiento
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lab-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>IoT Dashboard</h4>
                            <p>Monitoreo de dispositivos conectados</p>
                            <button type="button" class="crud-btn-action" onclick="openLab('iot')">
                                <i class="fas fa-chart-line"></i>
                                Monitorear
                            </button>
                        </div>
                        <div class="lab-status online">
                            <i class="fas fa-circle"></i>
                            Online
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Notificaciones y Alertas -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-bell"></i>
                    Centro de Notificaciones
                </h2>
                <p class="crud-section-subtitle">Mantente informado de las últimas actualizaciones del sistema</p>
            </div>
            
            <div class="crud-form-body">
                <div class="notifications-container">
                    @if (!empty($notificaciones))
                        @foreach ($notificaciones as $notificacion)
                            <div class="notification-item {{ $notificacion['tipo'] ?? 'info' }}">
                                <div class="notification-icon">
                                    <i class="fas fa-{{ $notificacion['icono'] ?? 'info-circle' }}"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">{{ $notificacion['titulo'] ?? 'Notificación' }}</div>
                                    <div class="notification-message">{{ $notificacion['mensaje'] ?? 'Sin mensaje' }}</div>
                                    <div class="notification-time">{{ $notificacion['tiempo'] ?? 'Ahora' }}</div>
                                </div>
                                <button class="notification-close" onclick="dismissNotification(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="notification-item success">
                            <div class="notification-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Sistema Operativo</div>
                                <div class="notification-message">Todos los módulos robóticos funcionan correctamente</div>
                                <div class="notification-time">Verificado hace 5 minutos</div>
                            </div>
                        </div>

                        <div class="notification-item info">
                            <div class="notification-icon">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Bienvenido a TECH HOME</div>
                                <div class="notification-message">Explora todas las funcionalidades disponibles en el sistema</div>
                                <div class="notification-time">Mensaje de bienvenida</div>
                            </div>
                        </div>

                        <div class="notification-item warning">
                            <div class="notification-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Mantenimiento Programado</div>
                                <div class="notification-message">El módulo de IA estará en mantenimiento el domingo de 2:00 AM a 6:00 AM</div>
                                <div class="notification-time">Programado para este domingo</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 20px;"></div>

    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para cambiar tabs de información
    document.querySelectorAll('.crud-info-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.crud-info-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.crud-info-pane').forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');
            const targetPane = document.getElementById(this.dataset.tab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // Actualizar reloj en tiempo real
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const dateString = now.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const clockElement = document.getElementById('techHomeTime');
        if (clockElement) {
            clockElement.innerHTML = `
                <div class="time">${timeString}</div>
                <div class="date">${dateString}</div>
            `;
        }
    }
    
    updateClock();
    setInterval(updateClock, 1000);

    // Navegación a diferentes secciones (para vista generalizada)
    window.showInfo = function(seccion) {
        const infoSecciones = {
            // Biblioteca Digital
            'robotica-libros': {
                titulo: 'Robótica Avanzada - Biblioteca Digital',
                descripcion: 'Colección especializada en robótica móvil, manipuladores industriales y sistemas autónomos.',
                caracteristicas: [
                    '• 845 títulos especializados en robótica',
                    '• Desde fundamentos hasta aplicaciones industriales',
                    '• Incluye casos de estudio y proyectos prácticos',
                    '• Autores reconocidos internacionalmente',
                    '• Actualizaciones constantes con nuevas tecnologías'
                ]
            },
            'ia-libros': {
                titulo: 'Inteligencia Artificial - Biblioteca Digital',
                descripcion: 'Recursos completos sobre Machine Learning, Deep Learning y Redes Neuronales.',
                caracteristicas: [
                    '• 632 títulos sobre IA y aprendizaje automático',
                    '• Tutoriales prácticos con Python y TensorFlow',
                    '• Teoría fundamental y matemáticas para IA',
                    '• Aplicaciones en visión artificial y PLN'
                ]
            },
            'programacion-libros': {
                titulo: 'Programación Avanzada - Biblioteca Digital',
                descripcion: 'Libros sobre lenguajes de programación para sistemas embebidos y robótica.',
                caracteristicas: [
                    '• C++, Python, ROS y MATLAB',
                    '• Programación de microcontroladores',
                    '• Algoritmos y estructuras de datos',
                    '• Desarrollo de software en tiempo real'
                ]
            },
            'control-libros': {
                titulo: 'Ingeniería de Control - Biblioteca Digital',
                descripcion: 'Fundamentos y aplicaciones de sistemas de control automático.',
                caracteristicas: [
                    '• Control PID, adaptativo y robusto',
                    '• Modelado matemático de sistemas dinámicos',
                    '• Análisis de estabilidad y respuesta en frecuencia',
                    '• Control de robots manipuladores'
                ]
            },
            
            // Centro de Componentes
            'microcontroladores': {
                titulo: 'Microcontroladores - Centro de Componentes',
                descripcion: 'Plataformas de hardware para el cerebro de tus robots.',
                caracteristicas: [
                    '• Arduino (Uno, Mega, Nano, Due)',
                    '• Raspberry Pi (4, 3B+, Zero W)',
                    '• ESP32 y ESP8266 para IoT',
                    '• STM32 y otros microcontroladores ARM'
                ]
            },
            'sensores': {
                titulo: 'Sensores Avanzados - Centro de Componentes',
                descripcion: 'Dispositivos para que tus robots perciban el entorno.',
                caracteristicas: [
                    '• LIDAR y sensores de distancia láser',
                    '• Cámaras y módulos de visión artificial',
                    '• IMUs (Acelerómetros y Giroscopios)',
                    '• Sensores ambientales y biométricos'
                ]
            },
            'actuadores': {
                titulo: 'Actuadores y Motores - Centro de Componentes',
                descripcion: 'Sistemas de movimiento y actuación para mecanismos robóticos.',
                caracteristicas: [
                    '• Servomotores digitales y analógicos',
                    '• Motores paso a paso NEMA',
                    '• Motores DC con encoder',
                    '• Actuadores lineales y neumáticos'
                ]
            },
            'alimentacion': {
                titulo: 'Fuentes y Baterías - Centro de Componentes',
                descripcion: 'Energía confiable para tus proyectos autónomos.',
                caracteristicas: [
                    '• Baterías LiPo de alta descarga',
                    '• Celdas 18650 y gestores de carga (BMS)',
                    '• Convertidores DC-DC (Buck/Boost)',
                    '• Fuentes de alimentación regulables'
                ]
            },

            // Cursos
            'robotica-basica': {
                titulo: 'Curso de Robótica Básica',
                descripcion: 'Inicia tu camino en el mundo de la robótica desde cero.',
                caracteristicas: [
                    '• Fundamentos de electrónica y mecánica',
                    '• Programación básica con Arduino',
                    '• Construcción de tu primer robot móvil',
                    '• Certificado de finalización incluido'
                ]
            },
            'machine-learning': {
                titulo: 'Curso de Machine Learning',
                descripcion: 'Domina los algoritmos que impulsan la inteligencia artificial moderna.',
                caracteristicas: [
                    '• Aprendizaje supervisado y no supervisado',
                    '• Redes neuronales y Deep Learning',
                    '• Proyectos prácticos con datos reales',
                    '• Implementación en sistemas robóticos'
                ]
            },
            'vision-artificial': {
                titulo: 'Curso de Visión Artificial',
                descripcion: 'Enseña a las máquinas a ver y entender el mundo visual.',
                caracteristicas: [
                    '• Procesamiento de imágenes con OpenCV',
                    '• Detección y reconocimiento de objetos',
                    '• Seguimiento visual y navegación',
                    '• Integración con cámaras robóticas'
                ]
            },
            'iot-automatizacion': {
                titulo: 'Curso de IoT y Automatización',
                descripcion: 'Conecta tus dispositivos y crea sistemas inteligentes.',
                caracteristicas: [
                    '• Protocolos de comunicación (MQTT, HTTP)',
                    '• Plataformas en la nube para IoT',
                    '• Domótica y control remoto',
                    '• Seguridad en dispositivos conectados'
                ]
            },

            // Acciones generales
            'biblioteca': {
                titulo: 'Biblioteca Digital TECH HOME',
                descripcion: 'Acceso ilimitado a miles de recursos educativos.',
                caracteristicas: [
                    '• Libros electrónicos y papers',
                    '• Revistas científicas y tesis',
                    '• Material multimedia y tutoriales',
                    '• Acceso 24/7 desde cualquier dispositivo'
                ]
            },
            'componentes': {
                titulo: 'Centro de Componentes TECH HOME',
                descripcion: 'Todo el hardware que necesitas en un solo lugar.',
                caracteristicas: [
                    '• Catálogo actualizado en tiempo real',
                    '• Envíos a todo el país',
                    '• Garantía y soporte técnico',
                    '• Descuentos para estudiantes'
                ]
            },
            'cursos': {
                titulo: 'Academia TECH HOME',
                descripcion: 'Formación de excelencia en tecnología y robótica.',
                caracteristicas: [
                    '• Instructores expertos en la industria',
                    '• Metodología práctica y proyectos reales',
                    '• Comunidad de aprendizaje activa',
                    '• Bolsa de trabajo para graduados'
                ]
            },
            'proyectos': {
                titulo: 'Laboratorio de Proyectos',
                descripcion: 'Innovación y desarrollo tecnológico en marcha.',
                caracteristicas: [
                    '• Proyectos de investigación aplicada',
                    '• Colaboración con empresas y universidades',
                    '• Oportunidades de pasantías',
                    '• Exhibición de prototipos'
                ]
            }
        };

        const info = infoSecciones[seccion];
        
        if (info) {
            let caracteristicasHtml = '';
            if (info.caracteristicas) {
                caracteristicasHtml = '<ul style="text-align: left; margin-top: 10px; color: #4b5563;">' + 
                    info.caracteristicas.map(c => `<li>${c}</li>`).join('') + 
                    '</ul>';
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: info.titulo,
                    html: `<p>${info.descripcion}</p>${caracteristicasHtml}`,
                    icon: 'info',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#dc2626',
                    width: '600px'
                });
            } else {
                alert(`${info.titulo}\n\n${info.descripcion}`);
            }
        } else {
            console.log('Sección no encontrada:', seccion);
        }
    };

    window.openLab = function(labType) {
        const labs = {
            'programming': 'IDE de Programación',
            'simulator': 'Simulador 3D',
            'ai': 'IA Training Hub',
            'iot': 'IoT Dashboard'
        };

        const labName = labs[labType] || 'Laboratorio Virtual';

        if (typeof Swal !== 'undefined') {
            let icon = 'success';
            let title = `Iniciando ${labName}`;
            let text = 'Preparando entorno virtual...';

            if (labType === 'ai') {
                icon = 'warning';
                title = 'Mantenimiento';
                text = 'El Hub de IA está en mantenimiento programado.';
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(`Abriendo ${labName}...`);
        }
    };

    window.dismissNotification = function(btn) {
        const notification = btn.closest('.notification-item');
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 300);
    };
});
</script>
@endsection