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
            @auth
                <a href="#" class="notifications-btn" title="Notificaciones">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notification-count" style="display: none;">0</span>
                </a>
            @endauth
            
            <!-- ============================================
                 TARJETA DE USUARIO Y CONTROLES
                 ============================================ -->
            <div class="user-info">
                @auth
                    <!-- ============================================
                         AVATAR DEL USUARIO
                         ============================================ -->
                    <div class="user-avatar" id="user-avatar">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar">
                        @else
                            {{ strtoupper(substr(auth()->user()->nombre, 0, 1) . substr(auth()->user()->apellido, 0, 1)) }}
                        @endif
                    </div>

                    <!-- ============================================
                         DATOS DEL USUARIO
                         ============================================ -->
                    <div class="user-details">
                        <h4 id="user-name">
                            {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                        </h4>
                        <span class="user-role" id="user-role">
                            {{ auth()->user()->roles()->first()->display_name ?? 'Usuario' }}
                        </span>
                        <span class="user-email" id="user-email">
                            {{ auth()->user()->email }}
                        </span>
                    </div>
                @endauth
                
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
                     BOTÓN CERRAR SESIÓN / INICIAR SESIÓN
                     ============================================ -->
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="logout-btn" title="Cerrar Sesión">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                @else
                    <form action="{{ route('auth.login') }}" method="GET" class="login-btn" title="Iniciar Sesión">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            Iniciar Sesión
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</div>