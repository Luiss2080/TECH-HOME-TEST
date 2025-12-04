<!-- ============================================
 HEADER TECH HOME - Instituto de Robótica
 ============================================ -->
<div class="tech-header-container">
    <header class="tech-header" id="techHeader">
        <!-- Fondo animado del header -->
        <div class="header-background">
            <div class="floating-particle particle-1"></div>
            <div class="floating-particle particle-2"></div>
            <div class="floating-particle particle-3"></div>
        </div>

        <!-- ============================================
         CONTENIDO PRINCIPAL DEL HEADER
         ============================================ -->
        <div class="header-content">
            <!-- ============================================
             SECCIÓN IZQUIERDA - Logo y Bienvenida
             ============================================ -->
            <div class="header-left">
                <div class="logo-section">
                    <div class="logo-container">
                        <img src="{{ asset('imagenes/logos/LogoTech.png') }}" alt="Tech Home Logo" class="header-logo">
                    </div>
                    <div class="welcome-text">
                        <h2 class="welcome-title">Bienvenido al Instituto de Robótica</h2>
                        <span class="welcome-subtitle">TECH HOME</span>
                    </div>
                </div>
            </div>

            <!-- ============================================
             SECCIÓN DERECHA - Controles de usuario
             ============================================ -->
            <div class="header-right">

                @auth
                    <!-- ============================================
                     INFORMACIÓN DE FECHA Y HORA
                     ============================================ -->
                    <div class="datetime-section">
                        <div class="datetime-item">
                            <i class="fas fa-calendar-day"></i>
                            <span id="current-date" class="datetime-text"></span>
                        </div>
                        <div class="datetime-item">
                            <i class="fas fa-clock"></i>
                            <span id="current-time" class="datetime-text"></span>
                        </div>
                    </div>

                    <!-- ============================================
                     BOTÓN DE NOTIFICACIONES
                     ============================================ -->
                    <div class="notifications-container">
                        <button class="notifications-btn" id="notificationsBtn" title="Notificaciones">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge" id="notificationCount">3</span>
                        </button>
                        
                        <!-- Dropdown de notificaciones -->
                        <div class="notifications-dropdown" id="notificationsDropdown">
                            <div class="dropdown-header">
                                <h6>Notificaciones</h6>
                                <span class="mark-all-read">Marcar como leídas</span>
                            </div>
                            <div class="notifications-list">
                                <div class="notification-item">
                                    <i class="fas fa-user-plus notification-icon"></i>
                                    <div class="notification-content">
                                        <p>Nuevo estudiante registrado</p>
                                        <span class="notification-time">Hace 2 minutos</span>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <i class="fas fa-book notification-icon"></i>
                                    <div class="notification-content">
                                        <p>Nuevo material subido</p>
                                        <span class="notification-time">Hace 15 minutos</span>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <i class="fas fa-graduation-cap notification-icon"></i>
                                    <div class="notification-content">
                                        <p>Curso completado por estudiante</p>
                                        <span class="notification-time">Hace 1 hora</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-footer">
                                <a href="#" class="view-all-btn">Ver todas</a>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================
                     PERFIL DE USUARIO CON DROPDOWN
                     ============================================ -->
                    <div class="user-profile-container">
                        <div class="user-profile" id="userProfile">
                            <!-- Avatar del usuario -->
                            <div class="user-avatar">
                                @php
                                    $user = auth()->user();
                                    $avatar = $user->avatar ?? null;
                                    
                                    if ($avatar && file_exists(public_path($avatar))) {
                                        echo '<img src="' . asset($avatar) . '" alt="Avatar" class="avatar-image">';
                                    } else {
                                        $nombre = $user->nombre ?? 'U';
                                        $apellido = $user->apellido ?? 'S';
                                        $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
                                        echo '<span class="avatar-initials">' . $iniciales . '</span>';
                                    }
                                @endphp
                            </div>
                            
                            <!-- Información del usuario -->
                            <div class="user-info">
                                <h6 class="user-name">{{ $user->nombre ?? 'Usuario' }} {{ $user->apellido ?? '' }}</h6>
                                <span class="user-role">
                                    @php
                                        $roles = $user ? $user->roles : [];
                                        if ($roles && count($roles) > 0) {
                                            echo ucfirst($roles[0]->nombre);
                                        } else {
                                            echo 'Usuario';
                                        }
                                    @endphp
                                </span>
                            </div>
                            
                            <!-- Indicador de dropdown -->
                            <div class="dropdown-indicator">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>

                        <!-- Dropdown del perfil -->
                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="dropdown-header">
                                <div class="header-avatar">
                                    @php
                                        if ($avatar && file_exists(public_path($avatar))) {
                                            echo '<img src="' . asset($avatar) . '" alt="Avatar" class="header-avatar-image">';
                                        } else {
                                            $iniciales = strtoupper(substr($user->nombre ?? 'U', 0, 1) . substr($user->apellido ?? 'S', 0, 1));
                                            echo '<span class="header-avatar-initials">' . $iniciales . '</span>';
                                        }
                                    @endphp
                                </div>
                                <div class="header-info">
                                    <h6>{{ $user->nombre ?? 'Usuario' }} {{ $user->apellido ?? '' }}</h6>
                                    <span>{{ $user->email ?? 'usuario@email.com' }}</span>
                                </div>
                            </div>
                            
                            <div class="dropdown-divider"></div>
                            
                            <div class="dropdown-menu">
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    <span>Ver Perfil</span>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-cog"></i>
                                    <span>Configuración</span>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-edit"></i>
                                    <span>Editar Perfil</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-question-circle"></i>
                                    <span>Ayuda</span>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-headset"></i>
                                    <span>Soporte</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('auth.logout') }}" method="POST" class="logout-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item logout-item">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- ============================================
                     BOTONES PARA USUARIOS NO AUTENTICADOS
                     ============================================ -->
                    <div class="guest-actions">
                        <a href="{{ route('auth.login') }}" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Iniciar Sesión</span>
                        </a>
                        <a href="{{ route('auth.register') }}" class="register-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Registrarse</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>
</div>