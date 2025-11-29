document.addEventListener("DOMContentLoaded", function () {
    // Toggle password visibility
    document.querySelectorAll(".password-toggle").forEach((toggle) => {
        toggle.addEventListener("click", function () {
            const targetId = this.getAttribute("data-target");
            const passwordInput = document.getElementById(targetId);
            const type =
                passwordInput.getAttribute("type") === "password"
                    ? "text"
                    : "password";
            passwordInput.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    });

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

    // Validación en tiempo real de contraseñas
    const password = document.getElementById("password");
    const passwordConfirmation = document.getElementById(
        "password_confirmation"
    );
    const strengthProgress = document.getElementById("strength-progress");
    const strengthText = document.getElementById("strength-text");

    // Elementos de requisitos
    const reqLength = document.getElementById("req-length");
    const reqUppercase = document.getElementById("req-uppercase");
    const reqLowercase = document.getElementById("req-lowercase");
    const reqNumber = document.getElementById("req-number");

    function checkPasswordStrength(password) {
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
        };

        // Actualizar indicadores visuales de requisitos
        if (reqLength) updateRequirement(reqLength, requirements.length);
        if (reqUppercase)
            updateRequirement(reqUppercase, requirements.uppercase);
        if (reqLowercase)
            updateRequirement(reqLowercase, requirements.lowercase);
        if (reqNumber) updateRequirement(reqNumber, requirements.number);

        // Calcular puntuación de fortaleza
        const score = Object.values(requirements).filter(Boolean).length;

        let strength = "";
        let color = "";
        let percentage = 0;

        switch (score) {
            case 0:
            case 1:
                strength = "Muy débil";
                color = "#dc3545";
                percentage = 25;
                break;
            case 2:
                strength = "Débil";
                color = "#fd7e14";
                percentage = 50;
                break;
            case 3:
                strength = "Buena";
                color = "#ffc107";
                percentage = 75;
                break;
            case 4:
                strength = "Excelente";
                color = "#28a745";
                percentage = 100;
                break;
        }

        // Actualizar barra de progreso
        if (strengthProgress) {
            strengthProgress.style.width = percentage + "%";
            strengthProgress.style.backgroundColor = color;
        }
        if (strengthText) {
            strengthText.textContent = strength;
            strengthText.style.color = color;
        }

        return score === 4;
    }

    function updateRequirement(element, isMet) {
        const icon = element.querySelector(".requirement-icon");
        if (isMet) {
            element.classList.add("met");
            element.classList.remove("unmet");
            icon.classList.remove("fa-circle");
            icon.classList.add("fa-check-circle");
        } else {
            element.classList.add("unmet");
            element.classList.remove("met");
            icon.classList.remove("fa-check-circle");
            icon.classList.add("fa-circle");
        }
    }

    function validatePasswords() {
        if (
            password &&
            passwordConfirmation &&
            password.value &&
            passwordConfirmation.value
        ) {
            if (password.value === passwordConfirmation.value) {
                passwordConfirmation.style.borderColor = "#28a745";
                const icon =
                    passwordConfirmation.parentElement.querySelector(
                        ".input-icon"
                    );
                if (icon) icon.style.color = "#28a745";
            } else {
                passwordConfirmation.style.borderColor = "#dc3545";
                const icon =
                    passwordConfirmation.parentElement.querySelector(
                        ".input-icon"
                    );
                if (icon) icon.style.color = "#dc3545";
            }
        }
    }

    if (password) {
        password.addEventListener("input", function () {
            checkPasswordStrength(this.value);
            validatePasswords();
        });
    }

    if (passwordConfirmation) {
        passwordConfirmation.addEventListener("input", validatePasswords);
    }

    // Auto-dismiss alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll(".alert");
        alerts.forEach((alert) => {
            alert.style.opacity = "0";
            alert.style.transform = "translateY(-100%)";
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    console.log("🚀 Register page loaded");

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
    const config = window.registerConfig || {};
    const error = config.error;
    const success = config.success;
    const errors = config.errors || {};
    const loginRoute = config.loginRoute;

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
        customSwal
            .fire({
                icon: "success",
                title: "¡Cuenta creada!",
                html: `${success}<br><br><strong>🔑 Importante:</strong> Revisa tu email para activar tu cuenta antes de iniciar sesión.`,
                confirmButtonText: "Ir al Login",
                background: "#1f2937",
                color: "#fff",
                timer: 8000,
                timerProgressBar: true,
                showClass: {
                    popup: "animate__animated animate__fadeInDown animate__faster",
                },
                hideClass: {
                    popup: "animate__animated animate__fadeOutUp animate__faster",
                },
            })
            .then((result) => {
                if (
                    (result.isConfirmed ||
                        result.dismiss === Swal.DismissReason.timer) &&
                    loginRoute
                ) {
                    window.location.href = loginRoute;
                }
            });
    }

    // Mostrar errores de validación
    if (Object.keys(errors).length > 0) {
        let errorMessages = [];
        for (const [field, fieldErrors] of Object.entries(errors)) {
            if (Array.isArray(fieldErrors)) {
                fieldErrors.forEach((fieldError) => {
                    errorMessages.push(
                        `${
                            field.charAt(0).toUpperCase() + field.slice(1)
                        }: ${fieldError}`
                    );
                });
            }
        }

        if (errorMessages.length > 0) {
            let errorMessage = "Por favor corrige los siguientes errores:\n\n";
            errorMessages.forEach((errorMsg) => {
                errorMessage += `• ${errorMsg}\n`;
            });

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
});
