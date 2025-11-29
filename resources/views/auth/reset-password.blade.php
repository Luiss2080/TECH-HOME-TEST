<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Tech Home Bolivia</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">
    <!-- CSS Personalizados -->
    <link rel="stylesheet" href="{{ asset('css/auth/reset-password.css') }}">
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

    <div class="reset-container">
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

            <h1 class="welcome-title">Nueva Contraseña</h1>
            <p class="welcome-text">
                Crea una nueva contraseña segura para tu cuenta. Asegúrate de que sea fácil de recordar para ti pero difícil de adivinar para otros
            </p>
            <div class="security-tips">
                <div class="tip-item">
                    <i class="fas fa-check"></i>
                    <span>Mínimo 8 caracteres</span>
                </div>
                <div class="tip-item">
                    <i class="fas fa-check"></i>
                    <span>Incluye números</span>
                </div>
                <div class="tip-item">
                    <i class="fas fa-check"></i>
                    <span>Usa símbolos especiales</span>
                </div>
            </div>
            <div class="copyright-text">
                © {{ date('Y') }} Tech Home Bolivia – Todos los derechos reservados
            </div>
        </div>

        <!-- Panel de reset -->
        <div class="reset-panel">
            <div class="reset-header">
                <h2 class="reset-title">Restablecer Contraseña</h2>
                <p class="reset-subtitle">Ingresa tu nueva contraseña para completar el proceso</p>
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('auth.reset-password.submit') }}">
                @csrf
                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" class="form-input @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $email ?? '') }}"
                               placeholder="tu-email@ejemplo.com" required readonly>
                        <i class="fas fa-envelope input-icon"></i>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nueva Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-input @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Mínimo 8 caracteres..." required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="password-strength" id="passwordStrength">
                            <div class="strength-bar">
                                <div class="strength-fill"></div>
                            </div>
                            <span class="strength-text">Fortaleza de la contraseña</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmar Nueva Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-input @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" name="password_confirmation" 
                               placeholder="Repite tu nueva contraseña..." required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye password-toggle" id="togglePasswordConfirm"></i>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="reset-btn">
                    <i class="fas fa-key"></i>
                    Actualizar Contraseña
                </button>
            </form>

            <!-- Información de seguridad -->
            <div class="security-info">
                <div class="security-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Una vez actualizada, tu sesión se cerrará automáticamente en otros dispositivos</span>
                </div>
                <div class="security-item">
                    <i class="fas fa-clock"></i>
                    <span>Este enlace expirará en 60 minutos por seguridad</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/auth/reset-password.js') }}"></script>
    
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