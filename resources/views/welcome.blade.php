@extends('layouts.app')

@section('title', 'Bienvenido a TECH HOME')

@section('styles')
<!-- Estilos específicos para el módulo CRUD - Vista Home -->
<link rel="stylesheet" href="{{ asset('css/vistas.css') }}">

<style>
/* Estilos específicos de TECH HOME - Vista Welcome */
.tech-home-hero {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(99, 102, 241, 0.05));
    border: 2px solid rgba(239, 68, 68, 0.1);
    position: relative;
    overflow: hidden;
}

.tech-home-icon {
    background: linear-gradient(135deg, var(--primary-red), #dc2626);
    box-shadow: 0 8px 32px rgba(239, 68, 68, 0.3);
}

.tech-home-title {
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-blue));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.tech-home-brand {
    display: inline-block;
    background: linear-gradient(135deg, var(--primary-red), #f59e0b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
    text-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
}

.tech-home-subtitle {
    color: var(--text-secondary);
    font-size: 1.1rem;
    line-height: 1.6;
}

.tech-home-status {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-indicator.online {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-indicator i {
    font-size: 0.8rem;
    animation: pulse 2s infinite;
}

.current-time {
    background: rgba(71, 85, 105, 0.1);
    color: var(--text-secondary);
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    border: 1px solid rgba(71, 85, 105, 0.2);
}

/* Elementos decorativos robóticos */
.tech-home-decorations {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    overflow: hidden;
}

.floating-robot {
    position: absolute;
    font-size: 1.5rem;
    color: rgba(239, 68, 68, 0.1);
    animation: float 6s ease-in-out infinite;
}

.floating-robot.robot-1 {
    top: 10%;
    right: 15%;
    animation-delay: 0s;
}

.floating-robot.robot-2 {
    top: 60%;
    right: 5%;
    animation-delay: 2s;
    font-size: 1.2rem;
}

.floating-robot.robot-3 {
    bottom: 20%;
    right: 20%;
    animation-delay: 4s;
    font-size: 1.8rem;
}

.circuit-lines {
    position: absolute;
    width: 100%;
    height: 100%;
}

.circuit-line {
    position: absolute;
    background: linear-gradient(90deg, transparent, rgba(239, 68, 68, 0.1), transparent);
    height: 1px;
    animation: circuit-flow 8s linear infinite;
}

.circuit-line.line-1 {
    top: 30%;
    right: 0;
    width: 40%;
    animation-delay: 0s;
}

.circuit-line.line-2 {
    top: 50%;
    right: 0;
    width: 60%;
    animation-delay: 2s;
}

.circuit-line.line-3 {
    top: 70%;
    right: 0;
    width: 35%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

@keyframes circuit-flow {
    0% { opacity: 0; transform: translateX(100%); }
    50% { opacity: 1; }
    100% { opacity: 0; transform: translateX(-100%); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Quick Actions Grid */
.tech-home-quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.quick-action-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.quick-action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    border-color: var(--primary-red);
}

.quick-action-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-blue));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin-bottom: 1rem;
}

.quick-action-content h4 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
    font-weight: 700;
}

.quick-action-content p {
    color: var(--text-secondary);
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.quick-action-stats {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--primary-red);
}

.stat-label {
    color: var(--text-secondary);
    font-size: 0.8rem;
}

.quick-action-arrow {
    position: absolute;
    top: 1rem;
    right: 1rem;
    color: var(--text-secondary);
    transition: all 0.3s ease;
}

.quick-action-card:hover .quick-action-arrow {
    color: var(--primary-red);
    transform: translateX(5px);
}

/* Stats Grid */
.tech-home-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-blue));
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.stat-info .stat-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.2rem;
}

.stat-info .stat-label {
    color: var(--text-secondary);
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
}

.stat-change {
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.stat-change.positive {
    color: var(--success-color);
}

.stat-change.negative {
    color: var(--danger-color);
}

/* Responsive */
@media (max-width: 768px) {
    .tech-home-quick-actions {
        grid-template-columns: 1fr;
    }
    
    .tech-home-stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .floating-robot {
        display: none;
    }
    
    .circuit-lines {
        display: none;
    }
}
</style>
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