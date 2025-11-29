<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">

    <!-- Headers anti-cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    @vite(['resources/css/modulos/auth/register.css', 'resources/js/modulos/auth/register.js'])
</head>

<body>
    <!-- Fondo animado -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div>
        <div class="floating-shapes shape-3"></div>
        <div class="floating-shapes shape-4"></div>
        <div class="floating-shapes shape-5"></div>
        <div class="floating-shapes shape-6"></div>
    </div>

    <div class="register-container">
        <!-- Panel de bienvenida -->
        <div class="welcome-panel">
            <div class="robot-icons">
                @for ($i = 0; $i < 20; $i++)
                    <i class="fas fa-robot robot-icon"></i>
                @endfor
            </div>

            <div class="logo-section">
                <div class="logo-container">
                    <img src="{{ asset('imagenes/logos/LogoTech-Home.png') }}" alt="Tech Home Logo" class="logo-img">
                </div>
                <div class="logo-underline"></div>
            </div>

            <h1 class="welcome-title">¡Únete a Nosotros!</h1>
            <p class="welcome-text">
                Crea tu cuenta y forma parte de la comunidad más innovadora de Bolivia.
                Accede a cursos de robótica, programación, electrónica y mucho más.
            </p>

            <div class="features-list">
                <div class="feature-item">
                    <i class="fas fa-robot"></i>
                    <span>Cursos de Robótica</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-code"></i>
                    <span>Programación</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-microchip"></i>
                    <span>Electrónica</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-brain"></i>
                    <span>Inteligencia Artificial</span>
                </div>
            </div>

            <div class="copyright-text">
                © 2025 Tech Home Bolivia – Todos los derechos reservados
            </div>
        </div>

        <!-- Panel de registro -->
        <div class="register-panel">
            <div class="register-header">
                <h2 class="register-title">Crear Cuenta</h2>
                <p class="register-subtitle">Completa tus datos para empezar tu aventura tecnológica</p>
            </div>

            <!-- Formulario -->
            <form method="POST" action="{{ route('register.store') }}" id="registerForm">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <div class="input-wrapper">
                            <input type="text" class="form-input" id="nombre" name="nombre"
                                placeholder="Tu nombre..."
                                value="{{ old('nombre') }}" required>
                            <i class="fas fa-user input-icon"></i>
                            <div class="tooltip">Ingresa tu nombre completo</div>
                        </div>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Apellido</label>
                        <div class="input-wrapper">
                            <input type="text" class="form-input" id="apellido" name="apellido"
                                placeholder="Tu apellido..."
                                value="{{ old('apellido') }}" required>
                            <i class="fas fa-user-tag input-icon"></i>
                            <div class="tooltip">Ingresa tu apellido completo</div>
                        </div>
                        @error('apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" class="form-input" id="email" name="email"
                            placeholder="ejemplo@correo.com"
                            value="{{ old('email') }}" required>
                        <i class="fas fa-envelope input-icon"></i>
                        <div class="tooltip">Usaremos este email para enviarte información importante</div>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Teléfono (Opcional)</label>
                        <div class="input-wrapper">
                            <input type="tel" class="form-input" id="telefono" name="telefono"
                                placeholder="+591 12345678"
                                value="{{ old('telefono') }}">
                            <i class="fas fa-phone input-icon"></i>
                            <div class="tooltip">Número de contacto (opcional)</div>
                        </div>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Fecha de Nacimiento (Opcional)</label>
                        <div class="input-wrapper">
                            <input type="date" class="form-input" id="fecha_nacimiento" name="fecha_nacimiento"
                                value="{{ old('fecha_nacimiento') }}">
                            <i class="fas fa-calendar input-icon"></i>
                            <div class="tooltip">Tu fecha de nacimiento (opcional)</div>
                        </div>
                        @error('fecha_nacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" class="form-input" id="password" name="password"
                                placeholder="Mínimo 8 caracteres, mayúscula y número..." required>
                            <i class="fas fa-lock input-icon"></i>
                            <i class="fas fa-eye password-toggle" data-target="password"></i>
                            <div class="tooltip">Debe tener al menos 8 caracteres, una mayúscula y un número</div>
                        </div>
                        
                        <!-- Indicador de seguridad de contraseña -->
                        <div class="password-strength" id="password-strength">
                            <div class="strength-bar">
                                <div class="strength-progress" id="strength-progress"></div>
                            </div>
                            <div class="strength-text" id="strength-text">Ingresa tu contraseña</div>
                        </div>

                        <!-- Lista de requisitos -->
                        <div class="password-requirements">
                            <div class="requirement" id="req-length">
                                <i class="fas fa-circle requirement-icon"></i>
                                <span>Mínimo 8 caracteres</span>
                            </div>
                            <div class="requirement" id="req-uppercase">
                                <i class="fas fa-circle requirement-icon"></i>
                                <span>Una letra mayúscula</span>
                            </div>
                            <div class="requirement" id="req-lowercase">
                                <i class="fas fa-circle requirement-icon"></i>
                                <span>Una letra minúscula</span>
                            </div>
                            <div class="requirement" id="req-number">
                                <i class="fas fa-circle requirement-icon"></i>
                                <span>Un número</span>
                            </div>
                        </div>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmar Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" class="form-input" id="password_confirmation" name="password_confirmation"
                                placeholder="Repite tu contraseña..." required>
                            <i class="fas fa-lock input-icon"></i>
                            <i class="fas fa-eye password-toggle" data-target="password_confirmation"></i>
                            <div class="tooltip">Debe coincidir con la contraseña anterior</div>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-info">
                    <div class="info-box">
                        <i class="fas fa-gift"></i>
                        <div>
                            <strong>¡Acceso Especial!</strong>
                            <p>Como nuevo usuario, tendrás acceso completo por 3 días para explorar todo nuestro contenido.</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i>
                    Crear Mi Cuenta
                </button>
            </form>

            <!-- Redes sociales -->
            <div class="divider">
                <p class="register-subtitle">¿Tienes dudas? ¡Contáctanos!</p>
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

            <div class="login-link">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>

    <script>
        window.registerConfig = {
            error: @json(session('error')),
            success: @json(session('success')),
            errors: @json($errors->toArray()),
            loginRoute: '{{ route('login') }}'
        };
    </script>
</body>

</html>