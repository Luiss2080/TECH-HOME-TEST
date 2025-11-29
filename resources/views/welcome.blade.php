@extends('layouts.app')

@section('title', 'Bienvenido - Tech Home')

@section('styles')
    @vite(['resources/css/modulos/home/welcome.css'])
@endsection

@section('content')
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
                                <div class="stat-number">{{ $estadisticas['componentes_total'] ?? '1,523' }}</div>
                                <div class="stat-label">Componentes</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-plus"></i>
                                    +{{ $estadisticas['componentes_nuevos'] ?? '8' }} este mes
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">{{ $estadisticas['cursos_total'] ?? '45' }}</div>
                                <div class="stat-label">Cursos Activos</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-plus"></i>
                                    +{{ $estadisticas['cursos_nuevos'] ?? '3' }} este mes
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">{{ $estadisticas['usuarios_total'] ?? '892' }}</div>
                                <div class="stat-label">Usuarios Registrados</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-plus"></i>
                                    +{{ $estadisticas['usuarios_nuevos'] ?? '27' }} este mes
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="crud-info-pane" id="rendimiento">
                    <div class="tech-home-stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-server"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">99.9%</div>
                                <div class="stat-label">Uptime del Sistema</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-check"></i>
                                    Excelente
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">0.2s</div>
                                <div class="stat-label">Tiempo de Respuesta</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-down"></i>
                                    Optimizado
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="crud-info-pane" id="actividad">
                    <div class="tech-home-stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">{{ $estadisticas['visitas_hoy'] ?? '1,247' }}</div>
                                <div class="stat-label">Visitas Hoy</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-plus"></i>
                                    +12% vs ayer
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number">{{ $estadisticas['descargas_hoy'] ?? '89' }}</div>
                                <div class="stat-label">Descargas Hoy</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-plus"></i>
                                    +8% vs ayer
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
// Actualizar reloj en tiempo real
function updateTechHomeTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const element = document.getElementById('techHomeTime');
    if (element) {
        element.textContent = timeString;
    }
}

// Funciones para mostrar información
function showInfo(tipo) {
    let mensaje = '';
    let icono = '';
    
    switch(tipo) {
        case 'biblioteca':
            mensaje = 'Biblioteca Digital: Acceso a recursos académicos especializados en robótica, IA y tecnología.';
            icono = 'fas fa-book';
            break;
        case 'componentes':
            mensaje = 'Centro de Componentes: Catálogo de hardware y componentes para proyectos robóticos.';
            icono = 'fas fa-microchip';
            break;
        case 'cursos':
            mensaje = 'Cursos Especializados: Formación avanzada en robótica, programación e inteligencia artificial.';
            icono = 'fas fa-graduation-cap';
            break;
        case 'proyectos':
            mensaje = 'Proyectos Innovadores: Desarrollos tecnológicos de vanguardia en curso.';
            icono = 'fas fa-project-diagram';
            break;
    }
    
    // Mostrar modal o notificación
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'TECH HOME',
            text: mensaje,
            icon: 'info',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#ef4444'
        });
    } else {
        alert(mensaje);
    }
}

// Manejo de tabs
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar reloj
    updateTechHomeTime();
    setInterval(updateTechHomeTime, 1000);
    
    // Manejo de tabs
    const tabs = document.querySelectorAll('.crud-info-tab');
    const panes = document.querySelectorAll('.crud-info-pane');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remover clases activas
            tabs.forEach(t => t.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));
            
            // Añadir clase activa
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
});
</script>
@endsection