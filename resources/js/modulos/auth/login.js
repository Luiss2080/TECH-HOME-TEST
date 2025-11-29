// Toggle password visibility
document.addEventListener("DOMContentLoaded", function () {
    const togglePassword = document.getElementById("togglePassword");
    if (togglePassword) {
        togglePassword.addEventListener("click", function () {
            const passwordInput = document.getElementById("password");
            const type =
                passwordInput.getAttribute("type") === "password"
                    ? "text"
                    : "password";
            passwordInput.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    }

    // Animaciones de inputs
    document.querySelectorAll(".form-input").forEach((input) => {
        input.addEventListener("focus", function () {
            this.parentElement.style.transform = "scale(1.02)";
            this.parentElement.style.zIndex = "10";
        });

        input.addEventListener("blur", function () {
            this.parentElement.style.transform = "scale(1)";
            this.parentElement.style.zIndex = "1";
        });
    });

    // Configuración personalizada de SweetAlert2
    const customSwal = Swal.mixin({
        customClass: {
            confirmButton: "swal-confirm-btn",
            cancelButton: "swal-cancel-btn",
            popup: "swal-popup",
        },
        buttonsStyling: false,
    });

    // Get config from window
    const config = window.loginConfig || {};
    const errors = config.errors || {};
    const error = config.error;
    const success = config.success;
    const blocked = config.blocked || [];

    // Blocked account logic
    if (blocked && Object.keys(blocked).length > 0) {
        let timeRemaining = blocked.time_remaining || 0;

        customSwal.fire({
            icon: "warning",
            title: "🔒 Cuenta Bloqueada",
            html: `
                <div style="text-align: left; margin: 20px 0;">
                    <p><strong>⚠️ Tu cuenta está temporalmente bloqueada</strong></p>
                    <ul style="margin: 15px 0; padding-left: 20px;">
                        <li>Demasiados intentos fallidos (${
                            blocked.attempts_made || 3
                        }/3)</li>
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
            confirmButtonText: "Entendido",
            allowOutsideClick: false,
            background: "#1f2937",
            color: "#fff",
            showClass: {
                popup: "animate__animated animate__fadeInDown animate__faster",
            },
            hideClass: {
                popup: "animate__animated animate__fadeOutUp animate__faster",
            },
            didOpen: () => {
                // Countdown timer
                const countdownElement = document.getElementById("countdown");
                if (countdownElement && timeRemaining > 0) {
                    const interval = setInterval(() => {
                        timeRemaining--;
                        countdownElement.textContent = timeRemaining;

                        if (timeRemaining <= 0) {
                            clearInterval(interval);
                            countdownElement.textContent = "0";
                            customSwal.update({
                                html: `
                                    <div style="text-align: center; margin: 20px 0;">
                                        <p><strong>✅ Tu cuenta ha sido desbloqueada</strong></p>
                                        <p>Ya puedes intentar iniciar sesión nuevamente.</p>
                                    </div>
                                `,
                                icon: "success",
                                title: "Cuenta Desbloqueada",
                                confirmButtonText: "Intentar de nuevo",
                            });
                        }
                    }, 60000); // Actualizar cada minuto
                }
            },
        });
    } else if (errors.general) {
        // General errors
        errors.general.forEach((errorMsg) => {
            customSwal.fire({
                icon: "error",
                title: "¡Error de acceso!",
                text: errorMsg,
                confirmButtonText: "Entendido",
                background: "#1f2937",
                color: "#fff",
                showClass: {
                    popup: "animate__animated animate__fadeInDown animate__faster",
                },
                hideClass: {
                    popup: "animate__animated animate__fadeOutUp animate__faster",
                },
            });
        });
    }

    if (error) {
        customSwal.fire({
            icon: "error",
            title: "¡Error!",
            text: error,
            confirmButtonText: "Entendido",
            background: "#1f2937",
            color: "#fff",
            showClass: {
                popup: "animate__animated animate__fadeInDown animate__faster",
            },
            hideClass: {
                popup: "animate__animated animate__fadeOutUp animate__faster",
            },
        });
    }

    if (success) {
        customSwal.fire({
            icon: "success",
            title: "¡Excelente!",
            text: success,
            confirmButtonText: "Continuar",
            background: "#1f2937",
            color: "#fff",
            timer: 5000,
            timerProgressBar: true,
            showClass: {
                popup: "animate__animated animate__fadeInDown animate__faster",
            },
            hideClass: {
                popup: "animate__animated animate__fadeOutUp animate__faster",
            },
        });
    }

    // Validation errors
    if (Object.keys(errors).length > 0) {
        const fieldErrors = {};
        for (const [field, messages] of Object.entries(errors)) {
            if (field !== "general" && Array.isArray(messages)) {
                fieldErrors[field] = messages[0];
            }
        }

        if (Object.keys(fieldErrors).length > 0) {
            let errorMessage = "Por favor corrige los siguientes errores:\n\n";
            for (const [field, message] of Object.entries(fieldErrors)) {
                errorMessage += `• ${
                    field.charAt(0).toUpperCase() + field.slice(1)
                }: ${message}\n`;
            }

            customSwal.fire({
                icon: "warning",
                title: "Errores de validación",
                text: errorMessage,
                confirmButtonText: "Corregir",
                background: "#1f2937",
                color: "#fff",
                showClass: {
                    popup: "animate__animated animate__fadeInDown animate__faster",
                },
                hideClass: {
                    popup: "animate__animated animate__fadeOutUp animate__faster",
                },
            });
        }
    }

    // Console debug
    console.log("🔐 Login page loaded with SweetAlert2");
    console.log("URL params:", window.location.search);

    // Verificar parámetros de logout
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("logout")) {
        console.log("✅ Logout exitoso detectado");
    }
    if (urlParams.get("error")) {
        console.log("❌ Error detectado:", urlParams.get("error"));
    }
    if (urlParams.get("timeout")) {
        console.log("⏰ Timeout detectado");
    }
});
