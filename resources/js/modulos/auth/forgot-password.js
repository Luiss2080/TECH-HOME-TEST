// Animación adicional para el formulario
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".reset-form");
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

    // Validación en tiempo real
    const emailInput = document.getElementById("email");
    if (emailInput) {
        emailInput.addEventListener("input", function () {
            if (this.value && this.validity.valid) {
                this.style.borderColor = "var(--success)";
                this.style.boxShadow = "0 0 0 3px rgba(16, 185, 129, 0.1)";
            } else if (this.value && !this.validity.valid) {
                this.style.borderColor = "var(--primary-red)";
                this.style.boxShadow = "0 0 0 3px rgba(220, 38, 38, 0.1)";
            } else {
                this.style.borderColor = "rgba(107, 114, 128, 0.2)";
                this.style.boxShadow = "none";
            }
        });
    }
});
