<?php
$title = $title ?? 'Bienvenido a TECH HOME';
$usuario = $usuario ?? null;
$estadisticas = $estadisticas ?? [];
$actividades_recientes = $actividades_recientes ?? [];
$notificaciones = $notificaciones ?? [];
?>

<!-- Estilos especÃ­ficos para el mÃ³dulo CRUD - Vista Home -->
@vite(['resources/css/home/welcome.css'])

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
                            Bienvenido al Instituto de RobÃ³tica
                            <span class="tech-home-brand">TECH HOME</span>
                        </h1>
                        <p class="crud-section-subtitle tech-home-subtitle">
                            <?php if ($usuario): ?>
                                Hola <?= htmlspecialchars($usuario->nombre) ?>, estÃ¡s conectado al futuro de la robÃ³tica y la tecnologÃ­a
                            <?php else: ?>
                                Portal de acceso al ecosistema tecnolÃ³gico mÃ¡s avanzado
                            <?php endif; ?>
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
            
            <!-- Elementos decorativos robÃ³ticos -->
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

        <!-- SecciÃ³n: Explorar TECH HOME -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-compass"></i>
                    Explora el Ecosistema TECH HOME
                </h2>
                <p class="crud-section-subtitle">Descubre las Ã¡reas principales de nuestro instituto de robÃ³tica</p>
            </div>
            
            <div class="crud-form-body">
                <div class="tech-home-quick-actions">
                    <div class="quick-action-card" onclick="showInfo('biblioteca')">
                        <div class="quick-action-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="quick-action-content">
                            <h4>Biblioteca Digital</h4>
                            <p>Recursos acadÃ©micos especializados</p>
                            <div class="quick-action-stats">
                                <span class="stat-number"><?= $estadisticas['libros'] ?? '2,847' ?></span>
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
                            <p>Hardware y tecnologÃ­a robÃ³tica</p>
                            <div class="quick-action-stats">
                                <span class="stat-number"><?= $estadisticas['componentes'] ?? '1,523' ?></span>
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
                            <p>FormaciÃ³n en robÃ³tica e IA</p>
                            <div class="quick-action-stats">
                                <span class="stat-number"><?= $estadisticas['cursos'] ?? '45' ?></span>
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
                            <p>Desarrollos tecnolÃ³gicos en curso</p>
                            <div class="quick-action-stats">
                                <span class="stat-number"><?= $estadisticas['proyectos'] ?? '128' ?></span>
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

        <!-- SecciÃ³n: EstadÃ­sticas del Sistema -->
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
                                    <div class="stat-number"><?= $estadisticas['libros_total'] ?? '2,847' ?></div>
                                    <div class="stat-label">Libros Especializados</div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-plus"></i>
                                        +<?= $estadisticas['libros_nuevos'] ?? '15' ?> este mes
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-microchip"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?= $estadisticas['componentes'] ?? '1,523' ?></div>
                                    <div class="stat-label">Componentes Disponibles</div>
                                    <div class="stat-change warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <?= $estadisticas['stock_bajo'] ?? '12' ?> stock bajo
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?= $estadisticas['cursos'] ?? '45' ?></div>
                                    <div class="stat-label">Cursos Activos</div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-users"></i>
                                        <?= $estadisticas['estudiantes_inscritos'] ?? '342' ?> inscritos
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
                                <div class="metric-value">99.8% - Ã“ptimo</div>
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
                                <div class="metric-value">45% - Ã“ptimo</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="actividad">
                        <div class="activity-feed">
                            <?php if (!empty($actividades_recientes)): ?>
                                <?php foreach (array_slice($actividades_recientes, 0, 5) as $actividad): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-<?= $actividad['tipo'] === 'usuario' ? 'user' : ($actividad['tipo'] === 'sistema' ? 'cog' : 'info-circle') ?>"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title"><?= htmlspecialchars($actividad['titulo'] ?? 'Actividad del sistema') ?></div>
                                            <div class="activity-description"><?= htmlspecialchars($actividad['descripcion'] ?? 'Sin descripciÃ³n') ?></div>
                                            <div class="activity-time"><?= $actividad['tiempo'] ?? 'Hace un momento' ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Sistema iniciado correctamente</div>
                                        <div class="activity-description">Todos los mÃ³dulos robÃ³ticos estÃ¡n operativos</div>
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
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SecciÃ³n: Biblioteca Digital -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-book-open"></i>
                    Biblioteca Digital Especializada
                </h2>
                <p class="crud-section-subtitle">Accede a recursos acadÃ©micos de vanguardia en robÃ³tica e inteligencia artificial</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>RobÃ³tica Avanzada</h4>
                            <p>Libros especializados en robÃ³tica mÃ³vil y manipuladores</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('robotica-libros')">
                                <i class="fas fa-book"></i>
                                Ver CatÃ¡logo
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            845 tÃ­tulos
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
                            632 tÃ­tulos
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>ProgramaciÃ³n Avanzada</h4>
                            <p>Lenguajes especializados para sistemas embebidos</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('programacion-libros')">
                                <i class="fas fa-code"></i>
                                Consultar
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            589 tÃ­tulos
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>IngenierÃ­a de Control</h4>
                            <p>Sistemas de control automÃ¡tico y teorÃ­a</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('control-libros')">
                                <i class="fas fa-cogs"></i>
                                Acceder
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            781 tÃ­tulos
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SecciÃ³n: Centro de Componentes -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-microchip"></i>
                    Centro de Componentes ElectrÃ³nicos
                </h2>
                <p class="crud-section-subtitle">Inventario completo de hardware especializado para proyectos robÃ³ticos</p>
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
                            <p>CÃ¡maras, LIDAR, ultrasÃ³nicos y tÃ¡ctiles</p>
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
                            <h4>Fuentes y BaterÃ­as</h4>
                            <p>Sistemas de alimentaciÃ³n especializados</p>
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

        <!-- SecciÃ³n: Cursos Especializados -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-graduation-cap"></i>
                    Cursos de RobÃ³tica e IA
                </h2>
                <p class="crud-section-subtitle">FormaciÃ³n especializada desde nivel bÃ¡sico hasta investigaciÃ³n avanzada</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-course-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>RobÃ³tica BÃ¡sica</h4>
                            <p>IntroducciÃ³n a la programaciÃ³n de robots</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('robotica-basica')">
                                <i class="fas fa-play"></i>
                                Comenzar
                            </button>
                        </div>
                        <div class="course-info">
                            <div class="course-level beginner">BÃ¡sico</div>
                            <div class="course-duration">40 horas</div>
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-course-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Machine Learning</h4>
                            <p>Algoritmos de aprendizaje automÃ¡tico aplicado</p>
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
                            <h4>VisiÃ³n Artificial</h4>
                            <p>Procesamiento de imÃ¡genes y reconocimiento</p>
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
                            <h4>IoT y AutomatizaciÃ³n</h4>
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
                <p class="crud-section-subtitle">Accede a las herramientas de desarrollo y simulaciÃ³n</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-lab-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>IDE de ProgramaciÃ³n</h4>
                            <p>Entorno integrado para desarrollo robÃ³tico</p>
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
                            <p>Pruebas virtuales de prototipos robÃ³ticos</p>
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

        <!-- SecciÃ³n: Notificaciones y Alertas -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-bell"></i>
                    Centro de Notificaciones
                </h2>
                <p class="crud-section-subtitle">Mantente informado de las Ãºltimas actualizaciones del sistema</p>
            </div>
            
            <div class="crud-form-body">
                <div class="notifications-container">
                    <?php if (!empty($notificaciones)): ?>
                        <?php foreach ($notificaciones as $notificacion): ?>
                            <div class="notification-item <?= $notificacion['tipo'] ?? 'info' ?>">
                                <div class="notification-icon">
                                    <i class="fas fa-<?= $notificacion['icono'] ?? 'info-circle' ?>"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title"><?= htmlspecialchars($notificacion['titulo'] ?? 'NotificaciÃ³n') ?></div>
                                    <div class="notification-message"><?= htmlspecialchars($notificacion['mensaje'] ?? 'Sin mensaje') ?></div>
                                    <div class="notification-time"><?= $notificacion['tiempo'] ?? 'Ahora' ?></div>
                                </div>
                                <button class="notification-close" onclick="dismissNotification(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="notification-item success">
                            <div class="notification-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Sistema Operativo</div>
                                <div class="notification-message">Todos los mÃ³dulos robÃ³ticos funcionan correctamente</div>
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
                                <div class="notification-message">El mÃ³dulo de IA estarÃ¡ en mantenimiento el domingo de 2:00 AM a 6:00 AM</div>
                                <div class="notification-time">Programado para este domingo</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Espacio de separaciÃ³n -->
        <div style="height: 20px;"></div>

    </div>
</div>

<!-- JavaScript especÃ­fico para la vista Home -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FunciÃ³n para cambiar tabs de informaciÃ³n
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

    // NavegaciÃ³n a diferentes secciones (para vista generalizada)
    window.showInfo = function(seccion) {
        const infoSecciones = {
            // Biblioteca Digital
            'robotica-libros': {
                titulo: 'RobÃ³tica Avanzada - Biblioteca Digital',
                descripcion: 'ColecciÃ³n especializada en robÃ³tica mÃ³vil, manipuladores industriales y sistemas autÃ³nomos.',
                caracteristicas: [
                    'â€¢ 845 tÃ­tulos especializados en robÃ³tica',
                    'â€¢ Desde fundamentos hasta aplicaciones industriales',
                    'â€¢ Incluye casos de estudio y proyectos prÃ¡cticos',
                    'â€¢ Autores reconocidos internacionalmente',
                    'â€¢ Actualizaciones constantes con nuevas tecnologÃ­as'
                ]
            },
            'ia-libros': {
                titulo: 'Inteligencia Artificial - Recursos AcadÃ©micos',
                descripcion: 'Biblioteca completa sobre machine learning, deep learning y sistemas inteligentes.',
                caracteristicas: [
                    'â€¢ 632 libros especializados en IA',
                    'â€¢ Algoritmos de aprendizaje automÃ¡tico',
                    'â€¢ Redes neuronales y deep learning',
                    'â€¢ Procesamiento de lenguaje natural',
                    'â€¢ Aplicaciones en robÃ³tica e industria'
                ]
            },
            'programacion-libros': {
                titulo: 'ProgramaciÃ³n Avanzada - Sistemas Embebidos',
                descripcion: 'Recursos especializados en lenguajes de programaciÃ³n para robÃ³tica y sistemas embebidos.',
                caracteristicas: [
                    'â€¢ 589 tÃ­tulos de programaciÃ³n especializada',
                    'â€¢ C/C++, Python, ROS y MATLAB',
                    'â€¢ ProgramaciÃ³n de microcontroladores',
                    'â€¢ Algoritmos para sistemas en tiempo real',
                    'â€¢ Desarrollo de firmware especializado'
                ]
            },
            'control-libros': {
                titulo: 'IngenierÃ­a de Control - TeorÃ­a y PrÃ¡ctica',
                descripcion: 'Biblioteca especializada en sistemas de control automÃ¡tico y teorÃ­a de control.',
                caracteristicas: [
                    'â€¢ 781 libros de teorÃ­a de control',
                    'â€¢ Control clÃ¡sico y moderno',
                    'â€¢ Sistemas de control digital',
                    'â€¢ Control adaptativo y robusto',
                    'â€¢ Aplicaciones en robÃ³tica industrial'
                ]
            },
            
            // Centro de Componentes
            'microcontroladores': {
                titulo: 'Microcontroladores y Sistemas Embebidos',
                descripcion: 'Inventario completo de microcontroladores, SBCs y sistemas de desarrollo.',
                caracteristicas: [
                    'â€¢ Arduino (Uno, Mega, Nano, ESP32)',
                    'â€¢ Raspberry Pi (4B, Zero, Compute Module)',
                    'â€¢ STM32 y microcontroladores ARM',
                    'â€¢ Kits de desarrollo y shields especializados',
                    'â€¢ Sistemas de prototipado rÃ¡pido'
                ]
            },
            'sensores': {
                titulo: 'Sensores Avanzados para RobÃ³tica',
                descripcion: 'Amplia gama de sensores especializados para percepciÃ³n robÃ³tica.',
                caracteristicas: [
                    'â€¢ CÃ¡maras RGB-D y sistemas de visiÃ³n',
                    'â€¢ Sensores LIDAR y ultrasÃ³nicos',
                    'â€¢ IMUs y sensores de orientaciÃ³n',
                    'â€¢ Sensores tÃ¡ctiles y de fuerza',
                    'â€¢ Sensores ambientales y quÃ­micos'
                ]
            },
            'actuadores': {
                titulo: 'Actuadores y Motores Especializados',
                descripcion: 'Sistema completo de actuadores para aplicaciones robÃ³ticas.',
                caracteristicas: [
                    'â€¢ Servomotores de alta precisiÃ³n',
                    'â€¢ Motores paso a paso y lineales',
                    'â€¢ Actuadores neumÃ¡ticos e hidrÃ¡ulicos',
                    'â€¢ Sistemas de tracciÃ³n y locomociÃ³n',
                    'â€¢ Controladores y drivers especializados'
                ]
            },
            'alimentacion': {
                titulo: 'Sistemas de AlimentaciÃ³n RobÃ³tica',
                descripcion: 'Soluciones completas de alimentaciÃ³n para sistemas robÃ³ticos.',
                caracteristicas: [
                    'â€¢ BaterÃ­as LiPo y Li-ion especializadas',
                    'â€¢ Fuentes de alimentaciÃ³n conmutadas',
                    'â€¢ Sistemas de carga inalÃ¡mbrica',
                    'â€¢ Reguladores de voltaje especializados',
                    'â€¢ Sistemas de gestiÃ³n de energÃ­a'
                ]
            },
            
            // Cursos Especializados
            'robotica-basica': {
                titulo: 'Curso: RobÃ³tica BÃ¡sica',
                descripcion: 'IntroducciÃ³n completa al mundo de la robÃ³tica, desde conceptos fundamentales hasta proyectos prÃ¡cticos.',
                caracteristicas: [
                    'â€¢ 40 horas de formaciÃ³n intensiva',
                    'â€¢ ProgramaciÃ³n bÃ¡sica de Arduino',
                    'â€¢ ConstrucciÃ³n de robots mÃ³viles',
                    'â€¢ Sensores y actuadores bÃ¡sicos',
                    'â€¢ Proyecto final certificado'
                ]
            },
            'machine-learning': {
                titulo: 'Curso: Machine Learning Aplicado',
                descripcion: 'FormaciÃ³n avanzada en algoritmos de aprendizaje automÃ¡tico con aplicaciones robÃ³ticas.',
                caracteristicas: [
                    'â€¢ 60 horas de contenido especializado',
                    'â€¢ Python, TensorFlow y PyTorch',
                    'â€¢ Algoritmos supervisados y no supervisados',
                    'â€¢ Redes neuronales profundas',
                    'â€¢ Proyectos con datasets reales'
                ]
            },
            'vision-artificial': {
                titulo: 'Curso: VisiÃ³n Artificial Avanzada',
                descripciÃ³n: 'Curso especializado en procesamiento de imÃ¡genes y sistemas de visiÃ³n para robÃ³tica.',
                caracteristicas: [
                    'â€¢ 80 horas de formaciÃ³n avanzada',
                    'â€¢ OpenCV y bibliotecas especializadas',
                    'â€¢ DetecciÃ³n y reconocimiento de objetos',
                    'â€¢ Sistemas de navegaciÃ³n visual',
                    'â€¢ IntegraciÃ³n con sistemas robÃ³ticos'
                ]
            },
            'iot-automatizacion': {
                titulo: 'Curso: IoT y AutomatizaciÃ³n Industrial',
                descripcion: 'FormaciÃ³n en Internet de las Cosas aplicado a la automatizaciÃ³n y robÃ³tica.',
                caracteristicas: [
                    'â€¢ 50 horas de contenido prÃ¡ctico',
                    'â€¢ Protocolos IoT (MQTT, CoAP, LoRaWAN)',
                    'â€¢ Sensores inalÃ¡mbricos y conectividad',
                    'â€¢ Plataformas cloud y edge computing',
                    'â€¢ Proyectos de automatizaciÃ³n industrial'
                ]
            },
            
            // Secciones generales
            'biblioteca': {
                titulo: 'Biblioteca Digital TECH HOME',
                descripcion: 'Accede a nuestra extensa colecciÃ³n de recursos acadÃ©micos especializados en robÃ³tica, inteligencia artificial, programaciÃ³n y tecnologÃ­a avanzada.',
                caracteristicas: [
                    'â€¢ MÃ¡s de 2,800 libros especializados en formato digital',
                    'â€¢ Recursos actualizados constantemente',
                    'â€¢ Acceso 24/7 desde cualquier dispositivo',
                    'â€¢ Sistema de bÃºsqueda avanzada por temas',
                    'â€¢ Biblioteca de proyectos y casos de estudio'
                ]
            },
            'componentes': {
                titulo: 'Centro de Componentes RobÃ³ticos',
                descripcion: 'Descubre nuestro inventario completo de componentes, sensores, actuadores y hardware especializado para desarrollo robÃ³tico.',
                caracteristicas: [
                    'â€¢ MÃ¡s de 1,500 componentes disponibles',
                    'â€¢ Sensores Arduino, Raspberry Pi y microcontroladores',
                    'â€¢ Actuadores y motores especializados',
                    'â€¢ Sistemas de visiÃ³n artificial',
                    'â€¢ Hardware para IA y machine learning'
                ]
            },
            'cursos': {
                titulo: 'Cursos de RobÃ³tica e IA',
                descripcion: 'Explora nuestro catÃ¡logo de cursos especializados, desde nivel bÃ¡sico hasta avanzado, impartidos por expertos en tecnologÃ­a.',
                caracteristicas: [
                    'â€¢ 45 cursos activos en diferentes niveles',
                    'â€¢ ProgramaciÃ³n de robots y sistemas autÃ³nomos',
                    'â€¢ Inteligencia artificial y machine learning',
                    'â€¢ VisiÃ³n por computadora y procesamiento de imÃ¡genes',
                    'â€¢ Certificaciones reconocidas internacionalmente'
                ]
            },
            'proyectos': {
                titulo: 'Proyectos de InnovaciÃ³n',
                descripcion: 'Conoce los proyectos innovadores que desarrollamos, desde prototipos acadÃ©micos hasta soluciones industriales reales.',
                caracteristicas: [
                    'â€¢ 128 proyectos en desarrollo activo',
                    'â€¢ Colaboraciones con empresas tecnolÃ³gicas',
                    'â€¢ Robots autÃ³nomos y sistemas inteligentes',
                    'â€¢ Soluciones IoT y automatizaciÃ³n industrial',
                    'â€¢ InvestigaciÃ³n en IA aplicada'
                ]
            }
        };

        const info = infoSecciones[seccion];
        if (info) {
            alert(`${info.titulo}\n\n${info.descripcion}\n\nCaracterÃ­sticas principales:\n${info.caracteristicas.join('\n')}\n\nÂ¡Registrate para acceder a todo el contenido!`);
        }
    };

    // Funciones para laboratorios virtuales
    window.openLab = function(labType) {
        const labNames = {
            'programming': 'IDE de ProgramaciÃ³n',
            'simulator': 'Simulador 3D',
            'ai': 'IA Training Hub',
            'iot': 'IoT Dashboard'
        };
        
        if (labType === 'ai') {
            alert('El laboratorio de IA estÃ¡ actualmente en mantenimiento. EstarÃ¡ disponible prÃ³ximamente.');
            return;
        }
        
        alert(`Iniciando ${labNames[labType]}...\n\nEsta funcionalidad se implementarÃ¡ prÃ³ximamente.`);
    };

    // FunciÃ³n para cerrar notificaciones
    window.dismissNotification = function(button) {
        const notification = button.closest('.notification-item');
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    };

    // Animaciones de entrada para las tarjetas
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observar elementos para animaciÃ³n
    document.querySelectorAll('.quick-action-card, .stat-card, .crud-action-card, .notification-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(el);
    });

    // Efecto hover mejorado para tarjetas de acciÃ³n rÃ¡pida
    document.querySelectorAll('.quick-action-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.querySelector('.quick-action-arrow').style.transform = 'translateX(5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.querySelector('.quick-action-arrow').style.transform = 'translateX(0)';
        });
    });

    // Actualizar estadÃ­sticas con animaciÃ³n
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            let current = 0;
            const increment = target / 50;
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.floor(current);
                    setTimeout(updateCounter, 30);
                } else {
                    counter.textContent = target;
                }
            };
            
            setTimeout(updateCounter, Math.random() * 2000);
        });
    }

    // Iniciar animaciÃ³n de contadores despuÃ©s de un delay
    setTimeout(animateCounters, 1000);

    // Efecto de respiraciÃ³n para elementos robÃ³ticos
    function breatheEffect() {
        const robots = document.querySelectorAll('.floating-robot');
        robots.forEach((robot, index) => {
            robot.style.transform = `translateY(${Math.sin(Date.now() * 0.001 + index) * 10}px)`;
        });
        requestAnimationFrame(breatheEffect);
    }
    breatheEffect();

    // Auto-dismiss para notificaciones despuÃ©s de 10 segundos
    setTimeout(() => {
        const notifications = document.querySelectorAll('.notification-item');
        notifications.forEach((notification, index) => {
            if (index > 0) { // Mantener la primera notificaciÃ³n
                setTimeout(() => {
                    notification.style.opacity = '0.7';
                }, index * 5000);
            }
        });
    }, 10000);
});
</script>

