<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Tech Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/modulos/auth/reset-password.css', 'resources/js/modulos/auth/reset-password.js'])
</head>
<body>
    <!-- Animación de fondo -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div>
        <div class="floating-shapes shape-3"></div>
        <div class="floating-shapes shape-4"></div>
    </div>

    <div class="reset-container">
        <div class="reset-card">
            <div class="logo">
                <i class="fas fa-lock"></i>
                <h2>Nueva Contraseña</h2>
                <p>Crea una contraseña segura para {{ $email }}</p>
            </div>

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.reset-password.submit') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-key" style="margin-right: 0.5rem; color: var(--primary-red);"></i>
                        Nueva Contraseña
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Mínimo 8 caracteres">
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-check-double" style="margin-right: 0.5rem; color: var(--primary-red);"></i>
                        Confirmar Contraseña
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" required placeholder="Repite la contraseña">
                        <i class="fas fa-eye password-toggle" id="togglePasswordConfirm"></i>
                    </div>
                    @error('password_confirmation')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="strength-meter">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText">Ingresa una contraseña</div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fas fa-shield-alt"></i>
                    Actualizar Contraseña
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('auth.login') }}">
                    <i class="fas fa-arrow-left"></i>
                    Volver al inicio de sesión
                </a>
            </div>
        </div>
    </div>
</body>
</html>