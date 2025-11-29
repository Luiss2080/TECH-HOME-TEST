/**
 * ============================================
 * FUNCIONALIDADES JAVASCRIPT PARA FORGOT PASSWORD
 * ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    initForgotPasswordForm();
});

/**
 * Inicializar formulario de recuperación de contraseña
 */
function initForgotPasswordForm() {
    // Animaciones de inputs
    setupInputAnimations();
    
    // Validación en tiempo real
    setupRealTimeValidation();
    
    // Configurar botón de envío
    setupSubmitButton();
    
    console.log('🔑 Forgot password page loaded successfully');
}

/**
 * Configurar animaciones de inputs
 */
function setupInputAnimations() {
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
}

/**
 * Configurar validación en tiempo real
 */
function setupRealTimeValidation() {
    const emailInput = document.getElementById('email');

    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            validateEmail(this);
        });

        emailInput.addEventListener('input', function() {
            // Limpiar errores mientras escribe
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
                const feedback = this.parentElement.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.remove();
                }
            }
        });
    }
}

/**
 * Configurar botón de envío
 */
function setupSubmitButton() {
    const form = document.querySelector('form');
    const submitBtn = document.querySelector('.forgot-btn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                showLoadingState(submitBtn);
                
                // Simular envío (aquí iría la lógica real de Laravel)
                setTimeout(() => {
                    hideLoadingState(submitBtn);
                    form.submit();
                }, 1500);
            }
        });
    }
}

/**
 * Validar formulario completo
 */
function validateForm() {
    const emailInput = document.getElementById('email');
    let isValid = true;
    
    if (emailInput) {
        if (!validateEmail(emailInput)) {
            isValid = false;
        }
    }
    
    return isValid;
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
 * Mostrar estado de carga en botón
 */
function showLoadingState(button) {
    const originalContent = button.innerHTML;
    button.dataset.originalContent = originalContent;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    button.disabled = true;
    button.style.opacity = '0.8';
    button.style.cursor = 'not-allowed';
}

/**
 * Ocultar estado de carga en botón
 */
function hideLoadingState(button) {
    button.innerHTML = button.dataset.originalContent || '<i class="fas fa-paper-plane"></i> Enviar Enlace de Recuperación';
    button.disabled = false;
    button.style.opacity = '1';
    button.style.cursor = 'pointer';
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
        errorMessage = errors.join('\n');
    } else if (typeof errors === 'string') {
        errorMessage = errors;
    } else {
        errorMessage = 'Ha ocurrido un error inesperado';
    }

    customSwal.fire({
        icon: 'error',
        title: 'Error al enviar',
        text: errorMessage,
        confirmButtonText: 'Intentar de nuevo',
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
        title: '¡Correo enviado!',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                <p>${message}</p>
                <div style="background: #f0f9ff; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #3b82f6;">
                    <strong>📧 Próximos pasos:</strong>
                    <ul style="margin: 10px 0; padding-left: 20px;">
                        <li>Revisa tu bandeja de entrada</li>
                        <li>Si no lo encuentras, verifica spam/correo no deseado</li>
                        <li>El enlace expira en 60 minutos</li>
                        <li>Puedes solicitar un nuevo enlace si es necesario</li>
                    </ul>
                </div>
            </div>
        `,
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