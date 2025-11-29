<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">
    
    <!-- Headers anti-cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    @vite(['resources/css/modulos/auth/otp-verification.css', 'resources/js/modulos/auth/otp-verification.js'])
</head>

<body class="otp-page-container">
    <!-- Fondo animado -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div>
        <div class="floating-shapes shape-3"></div>
        <div class="floating-shapes shape-4"></div>
    </div>

    <div class="otp-main-card">
        <!-- Sección de encabezado -->
        <div class="header-section">
            <div class="icon-container" style="margin-bottom: 20px;">
                <i class="fas fa-shield-alt" style="font-size: 64px;"></i>
            </div>
            
            <h1 style="color: var(--text-dark); font-size: 28px; font-weight: 700; margin-bottom: 15px;">Verificación de Seguridad</h1>
            <p style="color: #6b7280; margin-bottom: 25px; line-height: 1.6; font-size: 16px;">
                Hemos enviado un código de verificación de 6 dígitos a tu email registrado
            </p>
            
            <div class="code-display">
                <i class="fas fa-envelope" style="color: var(--primary-red); margin-right: 8px;"></i>
                <span style="font-family: 'Courier New', monospace; font-weight: 600; color: #374151;">{{ $email ?? '' }}</span>
            </div>
        </div>

        <!-- Sección del timer -->
        <div class="timer-section">
            <div class="timer-container-custom">
                <div style="color: #92400e; font-weight: bold; margin-bottom: 10px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; position: relative; z-index: 2;">
                    ⏱️ Tiempo restante
                </div>
                <div style="font-size: 48px; font-weight: bold; color: var(--primary-red); font-family: 'Courier New', monospace; position: relative; z-index: 2;" id="timer">01:00</div>
            </div>
        </div>

        <!-- Sección del formulario -->
        <div class="form-section">
            <form method="POST" action="{{ route('auth.verify.otp') }}" id="otpForm">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? '' }}">
                
                <!-- Campos OTP -->
                <div class="otp-input-container">
                    @for ($i = 1; $i <= 6; $i++)
                        <input type="text" 
                               class="otp-digit" 
                               maxlength="1" 
                               inputmode="numeric" 
                               pattern="[0-9]*"
                               name="otp_digit_{{ $i }}"
                               id="digit-{{ $i }}"
                               autocomplete="off"
                               required>
                    @endfor
                </div>
                <input type="hidden" name="otp_code" id="otp_code">
            </form>
        </div>

        <!-- Sección del botón -->
        <div class="button-section">
            <button type="submit" class="btn-primary-custom" id="verifyBtn" form="otpForm">
                <i class="fas fa-check" style="margin-right: 8px;"></i>
                Verificar Código
            </button>
            
            <div class="loading" id="loading" style="margin-top: 15px;">
                <div class="spinner"></div>
                <span>Verificando...</span>
            </div>
        </div>

        <!-- Sección de enlaces -->
        <div class="links-section">
            <!-- Reenviar código -->
            <div style="text-align: center; margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 14px; margin-bottom: 10px;">¿No recibiste el código?</p>
                <a href="#" style="color: var(--primary-red); text-decoration: none; font-weight: 600; padding: 8px 16px; border-radius: 8px; transition: all 0.3s ease; display: inline-block;" id="resendLink" onclick="resendCode()">
                    <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>
                    Reenviar código
                </a>
                <div id="resendTimer" style="color: #6b7280; font-size: 14px; margin-top: 8px; display: none;">
                    Podrás solicitar un nuevo código en: <span id="resendCountdown" style="font-family: 'Courier New', monospace; font-weight: bold;">30</span>s
                </div>
            </div>

            <!-- Enlace de regreso -->
            <div style="text-align: center;">
                <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; color: #6b7280; text-decoration: none; font-size: 14px; transition: all 0.3s ease;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                    Volver al inicio de sesión
                </a>
            </div>
        </div>

        <!-- Sección de información de seguridad -->
        <div class="security-section">
            <div style="background: rgba(220, 38, 38, 0.05); border: 1px solid rgba(220, 38, 38, 0.2); border-radius: 12px; padding: 20px; text-align: left;">
                <h4 style="color: var(--primary-red); font-weight: 600; margin-bottom: 15px; display: flex; align-items: center;">
                    <i class="fas fa-info-circle" style="margin-right: 8px;"></i> 
                    Información de seguridad
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="color: #374151; font-size: 14px; margin-bottom: 8px; display: flex; align-items: flex-start;">
                        <span style="color: #10b981; font-weight: bold; margin-right: 12px; margin-top: 2px;">✓</span>
                        Este código expira en 60 segundos
                    </li>
                    <li style="color: #374151; font-size: 14px; margin-bottom: 8px; display: flex; align-items: flex-start;">
                        <span style="color: #10b981; font-weight: bold; margin-right: 12px; margin-top: 2px;">✓</span>
                        Solo puede ser utilizado una vez
                    </li>
                    <li style="color: #374151; font-size: 14px; margin-bottom: 8px; display: flex; align-items: flex-start;">
                        <span style="color: #10b981; font-weight: bold; margin-right: 12px; margin-top: 2px;">✓</span>
                        Después de 3 intentos fallidos tu cuenta será bloqueada temporalmente
                    </li>
                    <li style="color: #374151; font-size: 14px; margin-bottom: 0; display: flex; align-items: flex-start;">
                        <span style="color: #10b981; font-weight: bold; margin-right: 12px; margin-top: 2px;">✓</span>
                        Si no solicitaste este acceso, cambia tu contraseña inmediatamente
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>

    <script>
        window.otpConfig = {
            timerDuration: {{ $timer_duration ?? 60 }},
            resendRoute: '{{ route("auth.resend.otp") }}',
            email: '{{ $email ?? '' }}',
            error: @json(session('error')),
            success: @json(session('success'))
        };
    </script>
</body>

</html>
