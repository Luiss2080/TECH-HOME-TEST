document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const inputs = document.querySelectorAll(".form-control");

    // Efecto de focus mejorado
    inputs.forEach((input) => {
        input.addEventListener("focus", function () {
            this.parentElement.style.transform = "scale(1.02)";
        });

        input.addEventListener("blur", function () {
            this.parentElement.style.transform = "scale(1)";
        });
    });

    // Toggle password visibility
    function togglePasswordVisibility(inputId, toggleId) {
        const toggle = document.getElementById(toggleId);
        if (toggle) {
            toggle.addEventListener("click", function () {
                const passwordInput = document.getElementById(inputId);
                const type =
                    passwordInput.getAttribute("type") === "password"
                        ? "text"
                        : "password";
                passwordInput.setAttribute("type", type);
                this.classList.toggle("fa-eye");
                this.classList.toggle("fa-eye-slash");
            });
        }
    }

    togglePasswordVisibility("password", "togglePassword");
    togglePasswordVisibility("password_confirmation", "togglePasswordConfirm");

    // Password strength checker
    const password = document.getElementById("password");
    const passwordConfirm = document.getElementById("password_confirmation");
    const strengthFill = document.getElementById("strengthFill");
    const strengthText = document.getElementById("strengthText");
    const submitBtn = document.getElementById("submitBtn");

    function checkPasswordStrength(pass) {
        let score = 0;
        let feedback = [];

        if (pass.length >= 8) score++;
        else feedback.push("mínimo 8 caracteres");

        if (/[a-z]/.test(pass) && /[A-Z]/.test(pass)) score++;
        else feedback.push("mayúsculas y minúsculas");

        if (/\d/.test(pass)) score++;
        else feedback.push("números");

        if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\?]/.test(pass)) score++;
        else feedback.push("símbolos especiales");

        const strength = ["Muy débil", "Débil", "Regular", "Buena", "Fuerte"];
        const colors = ["#dc2626", "#f59e0b", "#f59e0b", "#10b981", "#10b981"];

        if (strengthFill) {
            strengthFill.style.width = score * 25 + "%";
            strengthFill.style.background = `linear-gradient(135deg, ${
                colors[score] || "#e2e8f0"
            }, ${colors[score] || "#f1f5f9"})`;
        }

        if (strengthText) {
            strengthText.textContent =
                strength[score] || "Ingresa una contraseña";
            strengthText.style.color = colors[score] || "#6b7280";

            if (feedback.length > 0 && pass.length > 0) {
                strengthText.textContent +=
                    " (falta: " + feedback.join(", ") + ")";
                strengthText.style.color = "#6b7280";
            }
        }

        return score;
    }

    function validateForm() {
        if (!password || !passwordConfirm) return;

        const pass = password.value;
        const passConfirm = passwordConfirm.value;
        const strength = checkPasswordStrength(pass);

        // Validación en tiempo real de coincidencia
        if (passConfirm && pass !== passConfirm) {
            passwordConfirm.style.borderColor = "#dc2626";
            passwordConfirm.style.boxShadow =
                "0 0 0 3px rgba(220, 38, 38, 0.1)";
        } else if (passConfirm && pass === passConfirm) {
            passwordConfirm.style.borderColor = "#10b981";
            passwordConfirm.style.boxShadow =
                "0 0 0 3px rgba(16, 185, 129, 0.1)";
        } else {
            passwordConfirm.style.borderColor = "rgba(107, 114, 128, 0.2)";
            passwordConfirm.style.boxShadow = "none";
        }

        // Validación del password principal
        if (pass && strength >= 2) {
            password.style.borderColor = "#10b981";
            password.style.boxShadow = "0 0 0 3px rgba(16, 185, 129, 0.1)";
        } else if (pass && strength < 2) {
            password.style.borderColor = "#f59e0b";
            password.style.boxShadow = "0 0 0 3px rgba(245, 158, 11, 0.1)";
        } else {
            password.style.borderColor = "rgba(107, 114, 128, 0.2)";
            password.style.boxShadow = "none";
        }

        const isValid =
            strength >= 2 && pass === passConfirm && pass.length >= 8;
        if (submitBtn) {
            submitBtn.disabled = !isValid;

            // Cambiar estilo del botón según validez
            if (isValid) {
                submitBtn.style.background =
                    "linear-gradient(135deg, #dc2626, #ef4444)";
                submitBtn.style.cursor = "pointer";
            } else {
                submitBtn.style.background =
                    "linear-gradient(135deg, #9ca3af, #6b7280)";
                submitBtn.style.cursor = "not-allowed";
            }
        }
    }

    if (password) password.addEventListener("input", validateForm);
    if (passwordConfirm)
        passwordConfirm.addEventListener("input", validateForm);

    console.log("🔐 Reset Password - Sistema TECH HOME activo");
});
