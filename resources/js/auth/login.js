/**
 * ============================================
 * FUNCIONALIDADES JAVASCRIPT PARA LOGIN
 * ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    initLoginForm();
});

/**
 * Inicializar formulario de login
 */
function initLoginForm() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Animaciones de inputs
    const formInputs = document.querySelectorAll('.form-input');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.zIndex = '10';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
            this.parentElement.style.zIndex = '1';
        });
    });

    // Validación en tiempo real
    setupRealTimeValidation();
    
    console.log('🔐 Login page loaded successfully');
}

/**
 * Validación en tiempo real
 */
function setupRealTimeValidation() {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            validateEmail(this);
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('blur', function() {
            validatePassword(this);
        });
    }
}

/**
 * Validar email
 */
function validateEmail(input) {
    const email = input.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!email) {
        showFieldError(input, 'El correo electrónico es requerido');
        return false;
    }
    
    if (!emailRegex.test(email)) {
        showFieldError(input, 'Por favor ingresa un correo electrónico válido');
        return false;
    }
    
    showFieldSuccess(input);
    return true;
}

/**
 * Validar contraseña
 */
function validatePassword(input) {
    const password = input.value;
    
    if (!password) {
        showFieldError(input, 'La contraseña es requerida');
        return false;
    }
    
    if (password.length < 8) {
        showFieldError(input, 'La contraseña debe tener al menos 8 caracteres');
        return false;
    }
    
    showFieldSuccess(input);
    return true;
}

/**
 * Mostrar error en campo
 */
function showFieldError(input, message) {
    input.classList.add('is-invalid');
    input.classList.remove('is-valid');
    
    // Remover feedback anterior
    const existingFeedback = input.parentElement.querySelector('.invalid-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    // Agregar nuevo feedback
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = message;
    input.parentElement.appendChild(feedback);
    
    // Animación de shake
    input.parentElement.classList.add('animate-shake');
    setTimeout(() => {
        input.parentElement.classList.remove('animate-shake');
    }, 500);
}

/**
 * Mostrar éxito en campo
 */
function showFieldSuccess(input) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    
    // Remover feedback de error
    const existingFeedback = input.parentElement.querySelector('.invalid-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
}

/**
 * ============================================
 * FUNCIONES PARA SWEETALERT2
 * ============================================ */

/**
 * Configuración personalizada de SweetAlert2
 */
const customSwal = Swal.mixin({
    customClass: {
        confirmButton: 'swal-confirm-btn',
        cancelButton: 'swal-cancel-btn',
        popup: 'swal-popup'
    },
    buttonsStyling: false
});

/**
 * Mostrar alerta de error
 */
function showErrorAlert(errors) {
    let errorMessage = '';
    
    if (Array.isArray(errors)) {
        errorMessage = errors.join('\\n');
    } else if (typeof errors === 'string') {
        errorMessage = errors;
    } else {
        errorMessage = 'Ha ocurrido un error inesperado';
    }

    customSwal.fire({
        icon: 'error',
        title: '¡Error de acceso!',
        text: errorMessage,
        confirmButtonText: 'Entendido',
        background: '#1f2937',
        color: '#fff',
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        }
    });
}

/**
 * Mostrar alerta de éxito
 */
function showSuccessAlert(message) {
    customSwal.fire({
        icon: 'success',
        title: '¡Excelente!',
        text: message,
        confirmButtonText: 'Continuar',
        background: '#1f2937',
        color: '#fff',
        timer: 5000,
        timerProgressBar: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        }
    });
}

/**
 * Mostrar alerta de cuenta bloqueada
 */
function showBlockedAlert(blockedData) {
    let timeRemaining = blockedData.time_remaining || 0;
    
    customSwal.fire({
        icon: 'warning',
        title: '🔒 Cuenta Bloqueada',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                <p><strong>⚠️ Tu cuenta está temporalmente bloqueada</strong></p>
                <ul style="margin: 15px 0; padding-left: 20px;">
                    <li>Demasiados intentos fallidos (${blockedData.attempts_made || 3}/3)</li>
                    <li>Tiempo restante: <span id="countdown">${timeRemaining}</span> minutos</li>
                    <li>Se desbloqueará automáticamente</li>
                </ul>
                <div style="background: #fee; padding: 15px; border-radius: 8px; margin-top: 15px;">
                    <strong>💡 Consejos de seguridad:</strong>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li>Verifica que escribes correctamente tu email y contraseña</li>
                        <li>Asegúrate de no tener CAPS LOCK activado</li>
                        <li>Si olvidaste tu contraseña, usa "¿Olvidaste tu contraseña?"</li>
                    </ul>
                </div>
            </div>
        `,
        confirmButtonText: 'Entendido',
        allowOutsideClick: false,
        background: '#1f2937',
        color: '#fff',
        showClass: {
            popup: 'animate__animated animate__fadeInDown animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp animate__faster'
        },
        didOpen: () => {
            // Countdown timer
            const countdownElement = document.getElementById('countdown');
            if (countdownElement && timeRemaining > 0) {
                const interval = setInterval(() => {
                    timeRemaining--;
                    countdownElement.textContent = timeRemaining;
                    
                    if (timeRemaining <= 0) {
                        clearInterval(interval);
                        countdownElement.textContent = '0';
                        customSwal.update({
                            html: `
                                <div style="text-align: center; margin: 20px 0;">
                                    <p><strong>✅ Tu cuenta ha sido desbloqueada</strong></p>
                                    <p>Ya puedes intentar iniciar sesión nuevamente.</p>
                                </div>
                            `,
                            icon: 'success',
                            title: 'Cuenta Desbloqueada',
                            confirmButtonText: 'Intentar de nuevo'
                        });
                    }
                }, 60000); // Actualizar cada minuto
            }
        }
    });
}

/**
 * ============================================
 * UTILIDADES
 * ============================================ */

/**
 * Verificar parámetros de URL
 */
function checkUrlParameters() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('logout')) {
        console.log('✅ Logout exitoso detectado');
        showSuccessAlert('Sesión cerrada correctamente');
    }
    
    if (urlParams.get('error')) {
        console.log('❌ Error detectado:', urlParams.get('error'));
    }
    
    if (urlParams.get('timeout')) {
        console.log('⏰ Timeout detectado');
        showErrorAlert('Tu sesión ha expirado. Por favor, inicia sesión nuevamente.');
    }
}

// Ejecutar verificación de parámetros al cargar
document.addEventListener('DOMContentLoaded', checkUrlParameters);