<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">
    
    <!-- Headers anti-cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    @vite(['resources/css/modulos/auth/login.css', 'resources/js/modulos/auth/login.js'])
</head>

<body>
    <!-- Fondo animado -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div>
        <div class="floating-shapes shape-3"></div>
        <div class="floating-shapes shape-4"></div>
    </div>

    <div class="login-container">
        <!-- Panel de bienvenida -->
        <div class="welcome-panel">
            <div class="robot-icons">
                @for ($i = 0; $i < 16; $i++)
                    <i class="fas fa-robot robot-icon"></i>
                @endfor
            </div>

            <div class="logo-section">
                <div class="logo-container">
                    <img src="{{ asset('imagenes/logos/LogoTech-Home.png') }}" alt="Tech Home Logo" class="logo-img">
                </div>
                <div class="logo-underline"></div>
            </div>

            <h1 class="welcome-title">¡Bienvenido!</h1>
            <p class="welcome-text">
                Inicia sesión con tu cuenta académica y da el primer paso hacia una experiencia única llena de innovación y creatividad
            </p>
            <div class="copyright-text">
                © 2025 Tech Home Bolivia – Todos los derechos reservados
            </div>
        </div>

        <!-- Panel de login -->
        <div class="login-panel">
            <div class="login-header">
                <h2 class="login-title">Iniciar Sesión</h2>
                <p class="login-subtitle">Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('auth.login.submit') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" class="form-input" id="email" name="email"
                            value="{{ old('email') }}"
                            placeholder="Ingresa tu correo académico..." required>
                        <i class="fas fa-envelope input-icon"></i>
                        <div class="tooltip">Usa tu email registrado en la plataforma</div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-input" id="password" name="password"
                            placeholder="Ingresa tu contraseña..." required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="tooltip">Mínimo 8 caracteres</div>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" class="checkbox" id="remember" name="remember">
                        <span>Recordarme</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('auth.forgot-password') }}" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
            </form>

            <!-- Redes sociales -->
            <div class="divider" style="text-align: center;">
                <p class="login-subtitle">¿Tienes dudas o quieres saber más?</p>
                <p class="login-invitation" style="font-weight: bold; margin-top: 2px;">¡Contáctate con nosotros!</p>
            </div>

            <div class="social-buttons">
                <a href="#" class="social-btn tiktok-btn">
                    <img src="{{ asset('imagenes/logos/tiktok.webp') }}" alt="TikTok" class="social-logo">
                    TikTok
                </a>
                <a href="#" class="social-btn facebook-btn">
                    <img src="{{ asset('imagenes/logos/facebook.webp') }}" alt="Facebook" class="social-logo">
                    Facebook
                </a>
                <a href="#" class="social-btn instagram-btn">
                    <img src="{{ asset('imagenes/logos/Instagram.webp') }}" alt="Instagram" class="social-logo">
                    Instagram
                </a>
                <a href="#" class="social-btn whatsapp-btn">
                    <img src="{{ asset('imagenes/logos/wpps.webp') }}" alt="WhatsApp" class="social-logo">
                    WhatsApp
                </a>
            </div>

            <div class="register-link">
                ¿No tienes cuenta? <a href="{{ route('auth.register') }}">Regístrate aquí</a>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>

    <script>
        window.loginConfig = {
            errors: @json($errors->toArray()),
            error: @json(session('error')),
            success: @json(session('success')),
            blocked: @json(session('blocked') ?? [])
        };
    </script>
</body>

</html>