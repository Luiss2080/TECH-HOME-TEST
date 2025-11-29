<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Tech Home Bolivia</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">
    <!-- CSS Personalizados -->
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
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

    <div class="register-container">
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

            <h1 class="welcome-title">¡Únete a nosotros!</h1>
            <p class="welcome-text">
                Crea tu cuenta académica y comienza una experiencia educativa única en robótica y tecnología
            </p>
            <div class="copyright-text">
                © {{ date('Y') }} Tech Home Bolivia – Todos los derechos reservados
            </div>
        </div>

        <!-- Panel de registro -->
        <div class="register-panel">
            <div class="register-header">
                <h2 class="register-title">Crear Cuenta</h2>
                <p class="register-subtitle">Completa todos los campos para registrarte</p>
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <div class="input-wrapper">
                            <input type="text" class="form-input @error('nombre') is-invalid @enderror" 
                                   id="nombre" name="nombre" value="{{ old('nombre') }}"
                                   placeholder="Tu nombre..." required>
                            <i class="fas fa-user input-icon"></i>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Apellido</label>
                        <div class="input-wrapper">
                            <input type="text" class="form-input @error('apellido') is-invalid @enderror" 
                                   id="apellido" name="apellido" value="{{ old('apellido') }}"
                                   placeholder="Tu apellido..." required>
                            <i class="fas fa-user input-icon"></i>
                            @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" class="form-input @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}"
                               placeholder="tu-email@ejemplo.com" required>
                        <i class="fas fa-envelope input-icon"></i>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-input @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Mínimo 8 caracteres..." required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmar Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-input @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" name="password_confirmation" 
                               placeholder="Repite tu contraseña..." required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye password-toggle" id="togglePasswordConfirm"></i>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-options">
                    <label class="terms-check">
                        <input type="checkbox" class="checkbox" id="terms" name="terms" required>
                        <span>Acepto los <a href="#">términos y condiciones</a></span>
                    </label>
                </div>

                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i>
                    Crear Cuenta
                </button>
            </form>

            <div class="login-link">
                ¿Ya tienes cuenta? <a href="{{ route('auth.login') }}">Inicia sesión aquí</a>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/auth/register.js') }}"></script>
    
    <script>
        @if ($errors->any())
            showErrorAlert(@json($errors->all()));
        @endif

        @if (session('success'))
            showSuccessAlert('{{ session('success') }}');
        @endif
    </script>
</body>
</html>