<!-- ============================================
 COMPONENTE HEADER TECH HOME
 ============================================ -->
<div class="tech-header" id="techHeader">
    <!-- Circuitos tecnológicos de fondo -->
    <div class="tech-circuit"></div>

    <!-- ============================================
     CONTENIDO PRINCIPAL DEL HEADER
     ============================================ -->
    <div class="header-content">

        <!-- ============================================
     SECCIÓN DEL LOGO (POSICIONADO AL INICIO)
     ============================================ -->
        <div class="welcome-section">
            <div class="loga-container">
                <img src="{{ asset('imagenes/logos/LogoTech.png') }}" alt="Tech Home Logo" class="header-logo-img">
            </div>
        </div>


        <!-- ============================================
     SECCIÓN DERECHA: CONTROLES Y USUARIO
     ============================================ -->
        <div class="user-controls">

            <!-- ============================================
         BOTÓN DE NOTIFICACIONES
         ============================================ -->
            @if ($isAuth)
                <a href="#" class="notifications-btn" title="Notificaciones">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notification-count" style="display: none;">0</span>
                </a>
            @endif
            <!-- ============================================
         TARJETA DE USUARIO Y CONTROLES
         ============================================ -->
            <div class="user-info">
                @if ($isAuth)

                    <!-- ============================================
             AVATAR DEL USUARIO
             ============================================ -->
                    <div class="user-avatar" id="user-avatar">
                        @php
                            // Mostrar avatar o iniciales del usuario
                            if (!empty($_SESSION['usuario_avatar'])) {
                                echo '<img src="' . htmlspecialchars($_SESSION['usuario_avatar']) . '" alt="Avatar">';
                            } else {
                                // Mostrar iniciales si no hay avatar
                                $nombre = $_SESSION['usuario_nombre'] ?? 'U';
                                $apellido = $_SESSION['usuario_apellido'] ?? 'S';
                                $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
                                echo $iniciales;
                            }
                        @endphp
                    </div>

                    <!-- ============================================
             DATOS DEL USUARIO (DESDE SESIÓN PHP)
             ============================================ -->
                    <div class="user-details">
                        <h4 id="user-name">
                            @php
                                $user = auth();
                                echo $user ? htmlspecialchars($user->nombre . ' ' . $user->apellido) : 'Usuario';
                            @endphp
                        </h4>
                        <span class="user-role" id="user-role">
                            @php
                                if ($user) {
                                    $roles = $user->roles();
                                    echo htmlspecialchars(!empty($roles) ? $roles[0]['nombre'] : 'Usuario');
                                } else {
                                    echo 'Usuario';
                                }
                            @endphp
                        </span>
                        <span class="user-email" id="user-email">
                            {{ $user ? htmlspecialchars($user->email) : '' }}
                        </span>
                    </div>
                @endif
                <!-- ============================================
             INFORMACIÓN DE FECHA Y HORA
             ============================================ -->
                <div class="datetime-info">
                    <div class="datetime-item">
                        <i class="fas fa-calendar"></i>
                        <span id="current-date"></span>
                    </div>
                    <div class="datetime-item">
                        <i class="fas fa-clock"></i>
                        <span id="current-time"></span>
                    </div>
                </div>

                <!-- ============================================
             BOTÓN CERRAR SESIÓN MEJORADO
             ============================================ -->
                @if ($isAuth)
                    <form action="{{ route('logout') }}" method="POST" class="logout-btn" title="Cerrar Sesión">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                @else
                    <form action="{{ route('login') }}" method="GET" class="login-btn" title="Iniciar Sesión">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            Iniciar Sesión
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>