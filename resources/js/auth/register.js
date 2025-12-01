/**
 * ============================================
 * FUNCIONALIDADES JAVASCRIPT PARA REGISTRO
 * ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    initRegisterForm();
});

/**
 * Inicializar formulario de registro
 */
function initRegisterForm() {
    // Toggle password visibility
    setupPasswordToggles();
    
    // Animaciones de inputs
    setupInputAnimations();
    
    // Validación en tiempo real
    setupRealTimeValidation();
    
    // Validación de términos
    setupTermsValidation();
    
    console.log('📝 Register page loaded successfully');
}

/**
 * Configurar toggles de contraseña
 */
function setupPasswordToggles() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
    
    if (togglePasswordConfirm && passwordConfirmInput) {
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
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
    const nombreInput = document.getElementById('nombre');
    const apellidoInput = document.getElementById('apellido');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');

    if (nombreInput) {
        nombreInput.addEventListener('blur', function() {
            validateName(this, 'nombre');
        });
    }

    if (apellidoInput) {
        apellidoInput.addEventListener('blur', function() {
            validateName(this, 'apellido');
        });
    }

    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            validateEmail(this);
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            validatePassword(this);
            // Re-validar confirmación si ya tiene valor
            if (passwordConfirmInput.value) {
                validatePasswordConfirmation(passwordConfirmInput, this.value);
            }
        });
    }

    if (passwordConfirmInput) {
        passwordConfirmInput.addEventListener('blur', function() {
            validatePasswordConfirmation(this, passwordInput.value);
        });
    }
}

/**
 * Configurar validación de términos
 */
function setupTermsValidation() {
    const termsCheckbox = document.getElementById('terms');
    const registerBtn = document.querySelector('.register-btn');
    
    if (termsCheckbox && registerBtn) {
        termsCheckbox.addEventListener('change', function() {
            if (this.checked) {
                registerBtn.disabled = false;
                registerBtn.style.opacity = '1';
                registerBtn.style.cursor = 'pointer';
            } else {
                registerBtn.disabled = true;
                registerBtn.style.opacity = '0.6';
                registerBtn.style.cursor = 'not-allowed';
            }
        });
        
        // Estado inicial
        registerBtn.disabled = !termsCheckbox.checked;
        registerBtn.style.opacity = termsCheckbox.checked ? '1' : '0.6';
        registerBtn.style.cursor = termsCheckbox.checked ? 'pointer' : 'not-allowed';
    }
}

/**
 * Validar nombre o apellido
 */
function validateName(input, fieldName) {
    const value = input.value.trim();
    
    if (!value) {
        showFieldError(input, `El ${fieldName} es requerido`);
        return false;
    }
    
    if (value.length < 2) {
        showFieldError(input, `El ${fieldName} debe tener al menos 2 caracteres`);
        return false;
    }
    
    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(value)) {
        showFieldError(input, `El ${fieldName} solo puede contener letras`);
        return false;
    }
    
    showFieldSuccess(input);
    return true;
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
    let strength = 0;
    let messages = [];
    
    if (!password) {
        showFieldError(input, 'La contraseña es requerida');
        return false;
    }
    
    if (password.length < 8) {
        messages.push('Debe tener al menos 8 caracteres');
    } else {
        strength += 25;
    }
    
    if (!/[a-z]/.test(password)) {
        messages.push('Debe contener al menos una letra minúscula');
    } else {
        strength += 25;
    }
    
    if (!/[A-Z]/.test(password)) {
        messages.push('Debe contener al menos una letra mayúscula');
    } else {
        strength += 25;
    }
    
    if (!/\d/.test(password)) {
        messages.push('Debe contener al menos un número');
    } else {
        strength += 25;
    }
    
    // Actualizar barra de fortaleza
    updatePasswordStrength(strength);
    
    if (messages.length > 0) {
        showFieldError(input, messages[0]);
        return false;
    }
    
    showFieldSuccess(input);
    return true;
}

/**
 * Validar confirmación de contraseña
 */
function validatePasswordConfirmation(input, originalPassword) {
    const confirmation = input.value;
    
    if (!confirmation) {
        showFieldError(input, 'Debes confirmar tu contraseña');
        return false;
    }
    
    if (confirmation !== originalPassword) {
        showFieldError(input, 'Las contraseñas no coinciden');
        return false;
    }
    
    showFieldSuccess(input);
    return true;
}

/**
 * Actualizar barra de fortaleza de contraseña
 */
function updatePasswordStrength(strength) {
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    
    if (strengthBar && strengthText) {
        strengthBar.style.width = strength + '%';
        
        let text = '';
        let color = '';
        
        if (strength === 0) {
            text = 'Muy débil';
            color = '#ef4444';
        } else if (strength <= 25) {
            text = 'Débil';
            color = '#f97316';
        } else if (strength <= 50) {
            text = 'Regular';
            color = '#eab308';
        } else if (strength <= 75) {
            text = 'Buena';
            color = '#22c55e';
        } else {
            text = 'Muy fuerte';
            color = '#16a34a';
        }
        
        strengthText.textContent = text;
        strengthBar.style.backgroundColor = color;
    }
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
        errorMessage = 'Por favor corrige los siguientes errores:\n\n';
        errors.forEach((error, index) => {
            errorMessage += `• ${error}\n`;
        });
    } else if (typeof errors === 'string') {
        errorMessage = errors;
    } else {
        errorMessage = 'Ha ocurrido un error inesperado';
    }

    customSwal.fire({
        icon: 'error',
        title: 'Errores de validación',
        text: errorMessage,
        confirmButtonText: 'Corregir',
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
        title: '¡Registro exitoso!',
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
    }).then(() => {
        // Redireccionar al login después del éxito
        window.location.href = '/login';
    });
}