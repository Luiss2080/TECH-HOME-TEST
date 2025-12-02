@php
    $user = auth()->user();
    $roles = $user ? $user->roles : [];
    $isAdmin = $roles ? in_array('administrador', array_column($roles->toArray(), 'nombre')) : false;
    $isDocente = $roles ? in_array('docente', array_column($roles->toArray(), 'nombre')) : false;
    $isEstudiante = $roles ? in_array('estudiante', array_column($roles->toArray(), 'nombre')) : false;
@endphp

<!-- ============================================================================
 SIDEBAR REDISEÑADO - Instituto Tech Home
 ============================================================================ -->
<div class="ithr-navigation-panel">
    <!-- Fondo animado del sidebar -->
    <div class="ithr-animated-background">
        <div class="ithr-floating-element ithr-floating-element-1"></div>
        <div class="ithr-floating-element ithr-floating-element-2"></div>
        <div class="ithr-floating-element ithr-floating-element-3"></div>
    </div>

    <!-- ============================================================================
     SECCIÓN SUPERIOR - Logo y Branding
     ============================================================================ -->
    <div class="ithr-panel-header">
        <div class="ithr-brand-container">
            <div class="ithr-brand-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="ithr-brand-text">
                <h6 class="ithr-brand-title">TECH HOME</h6>
                <span class="ithr-brand-subtitle">Instituto de Robótica</span>
            </div>
        </div>
    </div>

    <!-- ============================================================================
     NAVEGACIÓN PRINCIPAL
     ============================================================================ -->
    <nav class="ithr-main-navigation">
        @auth
            <div class="ithr-nav-group">
                <h6 class="ithr-nav-group-title">Panel Principal</h6>
                <ul class="ithr-nav-list">
                    <li class="ithr-nav-item ithr-active">
                        <a href="{{ route('home') }}" class="ithr-nav-link">
                            <i class="fas fa-tachometer-alt ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Dashboard</span>
                            <div class="ithr-nav-indicator"></div>
                        </a>
                    </li>
                    <li class="ithr-nav-item">
                        <a href="{{ route('reportes') }}" class="ithr-nav-link">
                            <i class="fas fa-chart-bar ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Reportes</span>
                        </a>
                    </li>
                    <li class="ithr-nav-item">
                        <a href="{{ route('configuracion') }}" class="ithr-nav-link">
                            <i class="fas fa-cog ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Configuración</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endauth
        <div class="ithr-nav-group">
            <h6 class="ithr-nav-group-title">Gestión Académica</h6>
            <ul class="ithr-nav-list">
                <li class="ithr-nav-item">
                    <a href="{{ route('docente.dashboard') }}" class="ithr-nav-link">
                        <i class="fas fa-chalkboard-teacher ithr-nav-icon"></i>
                        <span class="ithr-nav-text">Docentes</span>
                        <span class="ithr-nav-badge">
                            @php
                                try {
                                    // Obtener contador dinámico de docentes activos
                                    $contadorDocentes = \App\Models\User::where('estado', '=', 1)
                                        ->whereRaw('id IN (SELECT model_id FROM model_has_roles WHERE model_type = "App\\\\Models\\\\User" AND role_id = (SELECT id FROM roles WHERE nombre = "docente"))')
                                        ->count();
                                    echo $contadorDocentes;
                                } catch (Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </span>
                    </a>
                </li>
                <li class="ithr-nav-item">
                    <a href="{{ route('estudiantes') }}" class="ithr-nav-link">
                        <i class="fas fa-user-graduate ithr-nav-icon"></i>
                        <span class="ithr-nav-text">Estudiantes</span>
                        <span class="ithr-nav-badge">
                            @php
                                try {
                                    // Obtener contador dinámico de estudiantes activos
                                    $contadorEstudiantes = \App\Models\User::where('estado', '=', 1)
                                        ->whereRaw('id IN (SELECT model_id FROM model_has_roles WHERE model_type = "App\\\\Models\\\\User" AND role_id = (SELECT id FROM roles WHERE nombre = "estudiante"))')
                                        ->count();
                                    echo $contadorEstudiantes;
                                } catch (Exception $e) {
                                    echo '125';
                                }
                            @endphp
                        </span>
                    </a>
                </li>
                <li class="ithr-nav-item">
                    <a href="{{ route('cursos') }}" class="ithr-nav-link">
                        <i class="fas fa-graduation-cap ithr-nav-icon"></i>
                        <span class="ithr-nav-text">Cursos</span>
                        <span class="ithr-nav-badge">35</span>
                    </a>
                </li>
                <li class="ithr-nav-item">
                    <a href="{{ route('usuarios') }}" class="ithr-nav-link">
                        <i class="fas fa-users-cog ithr-nav-icon"></i>
                        <span class="ithr-nav-text">Usuarios</span>
                        <span class="ithr-nav-badge">
                            @php
                                try {
                                    // Obtener contador dinámico de usuarios totales
                                    $contadorUsuarios = \App\Models\User::count();
                                    echo $contadorUsuarios;
                                } catch (Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="ithr-nav-group">
            <h6 class="ithr-nav-group-title">Recursos</h6>
            <ul class="ithr-nav-list">
                <li class="ithr-nav-item">
                    <a href="{{ route('libros') }}" class="ithr-nav-link">
                        <i class="fas fa-book ithr-nav-icon"></i>
                        <span class="ithr-nav-text">Biblioteca</span>
                        <span class="ithr-nav-badge">
                            @php
                                try {
                                    // Obtener contador dinámico de libros disponibles
                                    $contadorLibros = \App\Models\Libro::count();
                                    echo $contadorLibros;
                                } catch (Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </span>
                    </a>
                </li>
                <li class="ithr-nav-item">
                    <a href="{{ route('materiales') }}" class="ithr-nav-link">
                        <i class="fas fa-file-alt ithr-nav-icon"></i>
                        <span class="ithr-nav-text">Materiales</span>
                        <span class="ithr-nav-badge">
                            @php
                                try {
                                    // Obtener contador dinámico de materiales disponibles
                                    $contadorMateriales = \App\Models\Material::count();
                                    echo $contadorMateriales;
                                } catch (Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </span>
                    </a>
                </li>
                <li class="ithr-nav-item">
                    <a href="{{ route('laboratorios') }}" class="ithr-nav-link">
                        <i class="fas fa-flask ithr-nav-icon"></i>
                        <span class="ithr-nav-text">Laboratorios</span>
                        <span class="ithr-nav-badge">
                            @php
                                try {
                                    // Obtener contador dinámico de laboratorios disponibles
                                    $contadorLaboratorios = \App\Models\Laboratorio::count();
                                    echo $contadorLaboratorios;
                                } catch (Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </span>
                    </a>
                </li>
                <li class="ithr-nav-item">
                    <a href="{{ route('componentes') }}" class="ithr-nav-link">
                        <i class="fas fa-microchip ithr-nav-icon"></i>
                        <span class="ithr-nav-text">Componentes</span>
                        <span class="ithr-nav-badge">
                            @php
                                try {
                                    // Obtener contador dinámico de componentes disponibles
                                    $contadorComponentes = \App\Models\Componente::count();
                                    echo $contadorComponentes;
                                } catch (Exception $e) {
                                    echo '0';
                                }
                            @endphp
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- ============================================================================
     FOOTER REDISEÑADO CON NUEVAS FUNCIONES
     ============================================================================ -->
    <div class="ithr-panel-footer">
        <!-- Tarjeta de visita al sitio web -->
        <div class="ithr-website-promotion">
            <a href="https://techhomebolivia.com/index.php" target="_blank" class="ithr-website-link">
                <div class="ithr-website-card">
                    <div class="ithr-website-content">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Visitar Sitio Web</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Control de tema mejorado -->
        <div class="ithr-theme-control">
            <div class="ithr-theme-info">
                <div class="ithr-theme-icon-container">
                    <i class="fas fa-palette"></i>
                </div>
                <div class="ithr-theme-details">
                    <span class="ithr-theme-label">Modo Oscuro</span>
                    <span class="ithr-theme-description">Cambia el tema</span>
                </div>
            </div>

            <div class="ithr-theme-switch">
                <input type="checkbox" id="ithrThemeToggle" class="ithr-theme-checkbox">
                <label for="ithrThemeToggle" class="ithr-theme-slider">
                    <div class="ithr-theme-knob">
                        <i class="fas fa-sun ithr-switch-icon ithr-sun-icon"></i>
                        <i class="fas fa-moon ithr-switch-icon ithr-moon-icon"></i>
                    </div>
                </label>
            </div>
        </div>
    </div>
</div>