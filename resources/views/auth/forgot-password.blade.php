<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Tech Home Bolivia</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">
    <!-- CSS Personalizados -->
    <link rel="stylesheet" href="{{ asset('css/auth/forgot-password.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/animations.css') }}">
    
    <!-- Headers anti-cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
    <!-- Fondo animado -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div>
        <div class="floating-shapes shape-3"></div>
        <div class="floating-shapes shape-4"></div>
    </div>

    <div class="forgot-container">
        <!-- Panel de bienvenida -->
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

            <h1 class="welcome-title">Recuperar Acceso</h1>
            <p class="welcome-text">
                No te preocupes, es normal olvidar la contraseña. Te ayudamos a recuperar el acceso a tu cuenta
            </p>
            <div class="security-features">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Proceso Seguro</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-clock"></i>
                    <span>Rápido</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-envelope"></i>
                    <span>Por Email</span>
                </div>
            </div>
            <div class="copyright-text">
                © {{ date('Y') }} Tech Home Bolivia – Todos los derechos reservados
            </div>
        </div>

        <!-- Panel de recuperación -->
        <div class="forgot-panel">
            <div class="forgot-header">
                <h2 class="forgot-title">¿Olvidaste tu contraseña?</h2>
                <p class="forgot-subtitle">Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña</p>
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('auth.forgot-password.submit') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" class="form-input @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}"
                               placeholder="Ingresa tu correo registrado..." required>
                        <i class="fas fa-envelope input-icon"></i>
                        <div class="tooltip">Debe ser el mismo correo usado para crear tu cuenta</div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="forgot-btn">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Enlace de Recuperación
                </button>
            </form>

            <!-- Información adicional -->
            <div class="info-section">
                <div class="info-item">
                    <i class="fas fa-info-circle"></i>
                    <div class="info-text">
                        <strong>¿Qué sucede después?</strong>
                        <p>Recibirás un correo con un enlace seguro para crear una nueva contraseña. El enlace expira en 60 minutos.</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-question-circle"></i>
                    <div class="info-text">
                        <strong>¿No recibes el correo?</strong>
                        <p>Revisa tu carpeta de spam o correo no deseado. Si aún no lo encuentras, inténtalo de nuevo.</p>
                    </div>
                </div>
            </div>

            <div class="back-to-login">
                <a href="{{ route('auth.login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Volver al login
                </a>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/auth/forgot-password.js') }}"></script>
    
    <script>
        @if ($errors->any())
            showErrorAlert(@json($errors->all()));
        @endif

        @if (session('status'))
            showSuccessAlert('{{ session('status') }}');
        @endif
    </script>
</body>
</html>