<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Tech Home</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">
    
    @vite(['resources/css/modulos/auth/forgot-password.css', 'resources/js/modulos/auth/forgot-password.js'])
</head>
<body>
    <!-- Animación de fondo -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div>
        <div class="floating-shapes shape-3"></div>
        <div class="floating-shapes shape-4"></div>
    </div>

    <div class="forgot-container">
        <!-- Panel de bienvenida (Izquierda) -->
        <div class="welcome-panel">
            <div class="robot-icons">
                 @for ($i = 0; $i < 12; $i++)
                    <i class="fas fa-robot robot-icon"></i>
                @endfor
            </div>

            <div class="logo-section">
                <div class="logo-container">
                    <img src="{{ asset('imagenes/logos/LogoTech-Home.png') }}" alt="Tech Home Logo" class="logo-img">
                </div>
                <div class="logo-underline"></div>
            </div>

            <h1 class="welcome-title">Recuperación</h1>
            <p class="welcome-text">
                No te preocupes, es común olvidar la contraseña. Te ayudaremos a recuperar el acceso a tu cuenta de forma segura.
            </p>
            
            <div class="security-features">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Seguro</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-bolt"></i>
                    <span>Rápido</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-envelope-open-text"></i>
                    <span>Verificado</span>
                </div>
            </div>

            <div class="copyright-text">
                © 2025 Tech Home Bolivia – Todos los derechos reservados
            </div>
        </div>

        <!-- Panel de formulario (Derecha) -->
        <div class="forgot-panel">
            <div class="forgot-header">
                <h2 class="forgot-title">Recuperar Contraseña</h2>
                <p class="forgot-subtitle">Ingresa tu email para recibir un enlace de recuperación</p>
            </div>

            <!-- Alertas -->
             @if (session('success'))
                <div class="alert alert-success" style="color: green; margin-bottom: 1rem; text-align: center;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" style="color: red; margin-bottom: 1rem; text-align: center;">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.forgot-password.submit') }}">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Dirección de Email</label>
                    <div class="input-wrapper">
                        <input type="email" 
                               class="form-input @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               placeholder="tu-email@ejemplo.com"
                               autocomplete="email">
                        <i class="fas fa-envelope input-icon"></i>
                        <div class="tooltip">Ingresa el email asociado a tu cuenta</div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="forgot-btn">
                    <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>
                    Enviar Enlace de Recuperación
                </button>
            </form>

            <div class="info-section">
                <div class="info-item">
                    <i class="fas fa-info-circle"></i>
                    <div class="info-text">
                        <strong>¿Qué sucederá?</strong>
                        <p>Recibirás un correo con un enlace temporal para crear una nueva contraseña.</p>
                    </div>
                </div>
            </div>

            <div class="back-to-login">
                <a href="{{ route('auth.login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Volver al inicio de sesión
                </a>
            </div>
        </div>
    </div>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
</body>
</html>