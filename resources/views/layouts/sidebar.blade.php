@php
    $user = auth()->user();
    $roles = $user ? $user->roles : [];
    $isAdmin = $roles ? in_array('administrador', array_column($roles->toArray(), 'nombre')) : false;
    $isDocente = $roles ? in_array('docente', array_column($roles->toArray(), 'nombre')) : false;
    $isEstudiante = $roles ? in_array('estudiante', array_column($roles->toArray(), 'nombre')) : false;
    
    // Contadores dinámicos
    try {
        $contadorDocentes = \App\Models\User::whereHas('roles', function($query) {
            $query->where('nombre', 'docente');
        })->where('estado', 1)->count();
        
        $contadorEstudiantes = \App\Models\User::whereHas('roles', function($query) {
            $query->where('nombre', 'estudiante');
        })->where('estado', 1)->count();
        
        $contadorCursos = \App\Models\Curso::where('estado', 1)->count();
        $contadorUsuarios = \App\Models\User::where('estado', 1)->count();
        $contadorLibros = \App\Models\Libro::where('estado', 1)->count();
        $contadorMateriales = \App\Models\Material::where('estado', 1)->count();
        $contadorLaboratorios = \App\Models\Laboratorio::where('estado', 1)->count();
        $contadorComponentes = \App\Models\Componente::where('estado', 1)->count();
    } catch (Exception $e) {
        $contadorDocentes = 4;
        $contadorEstudiantes = 6;
        $contadorCursos = 35;
        $contadorUsuarios = 28;
        $contadorLibros = 30;
        $contadorMateriales = 20;
        $contadorLaboratorios = 5;
        $contadorComponentes = 43;
    }
@endphp

<!-- ============================================================================
 SIDEBAR TECH HOME - Instituto de Robótica
 ============================================================================ -->
<div class="tech-sidebar">
    <!-- Fondo animado del sidebar -->
    <div class="sidebar-background">
        <div class="floating-particle particle-1"></div>
        <div class="floating-particle particle-2"></div>
        <div class="floating-particle particle-3"></div>
    </div>

    <!-- ============================================================================
     HEADER DEL SIDEBAR - Logo y Branding
     ============================================================================ -->
    <div class="sidebar-header">
        <div class="brand-container">
            <div class="brand-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="brand-info">
                <h6 class="brand-title">TECH HOME</h6>
                <span class="brand-subtitle">Instituto de Robótica</span>
            </div>
        </div>
    </div>

    <!-- ============================================================================
     NAVEGACIÓN PRINCIPAL
     ============================================================================ -->
    <nav class="sidebar-navigation">
        <!-- Gestión Académica -->
        <div class="nav-section">
            <h6 class="section-title">GESTIÓN ACADÉMICA</h6>
            <ul class="nav-list">
                <li class="nav-item {{ request()->routeIs('docentes*') ? 'active' : '' }}">
                    <a href="{{ route('docente.dashboard') }}" class="nav-link">
                        <i class="fas fa-chalkboard-teacher nav-icon"></i>
                        <span class="nav-text">Docentes</span>
                        <span class="nav-counter">{{ $contadorDocentes }}</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('estudiantes*') ? 'active' : '' }}">
                    <a href="{{ route('estudiantes') }}" class="nav-link">
                        <i class="fas fa-user-graduate nav-icon"></i>
                        <span class="nav-text">Estudiantes</span>
                        <span class="nav-counter">{{ $contadorEstudiantes }}</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('cursos*') ? 'active' : '' }}">
                    <a href="{{ route('cursos') }}" class="nav-link">
                        <i class="fas fa-graduation-cap nav-icon"></i>
                        <span class="nav-text">Cursos</span>
                        <span class="nav-counter">{{ $contadorCursos }}</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('usuarios*') ? 'active' : '' }}">
                    <a href="{{ route('usuarios') }}" class="nav-link">
                        <i class="fas fa-users nav-icon"></i>
                        <span class="nav-text">Usuarios</span>
                        <span class="nav-counter">{{ $contadorUsuarios }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Recursos -->
        <div class="nav-section">
            <h6 class="section-title">RECURSOS</h6>
            <ul class="nav-list">
                <li class="nav-item {{ request()->routeIs('libros*') ? 'active' : '' }}">
                    <a href="{{ route('libros') }}" class="nav-link">
                        <i class="fas fa-book nav-icon"></i>
                        <span class="nav-text">Biblioteca</span>
                        <span class="nav-counter">{{ $contadorLibros }}</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('materiales*') ? 'active' : '' }}">
                    <a href="{{ route('materiales') }}" class="nav-link">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <span class="nav-text">Materiales</span>
                        <span class="nav-counter">{{ $contadorMateriales }}</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('laboratorios*') ? 'active' : '' }}">
                    <a href="{{ route('laboratorios') }}" class="nav-link">
                        <i class="fas fa-flask nav-icon"></i>
                        <span class="nav-text">Laboratorios</span>
                        <span class="nav-counter">{{ $contadorLaboratorios }}</span>
                    </a>
                </li>
                
                <li class="nav-item {{ request()->routeIs('componentes*') ? 'active' : '' }}">
                    <a href="{{ route('componentes') }}" class="nav-link">
                        <i class="fas fa-microchip nav-icon"></i>
                        <span class="nav-text">Componentes</span>
                        <span class="nav-counter">{{ $contadorComponentes }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- ============================================================================
     FOOTER DEL SIDEBAR - Funciones adicionales
     ============================================================================ -->
    <div class="sidebar-footer">
        <!-- Botón de visitar sitio web -->
        <div class="website-button">
            <a href="https://techhomebolivia.com/index.php" target="_blank" class="website-link">
                <i class="fas fa-external-link-alt"></i>
                <span>Visitar Sitio Web</span>
            </a>
        </div>

        <!-- Control de tema -->
        <div class="theme-control">
            <div class="theme-info">
                <i class="fas fa-moon theme-icon"></i>
                <span class="theme-label">Oscuro</span>
            </div>
            <div class="theme-toggle">
                <input type="checkbox" id="themeToggle" class="theme-checkbox">
                <label for="themeToggle" class="theme-slider">
                    <span class="slider-knob"></span>
                </label>
            </div>
        </div>
    </div>
</div>