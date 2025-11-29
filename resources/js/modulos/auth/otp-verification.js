document.addEventListener("DOMContentLoaded", function () {
    const config = window.otpConfig || {};
    const TIMER_DURATION = config.timerDuration || 60;
    const RESEND_COOLDOWN = 30;
    const resendRoute = config.resendRoute;
    const email = config.email;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    let timeLeft = TIMER_DURATION;
    let timerInterval;
    let resendCooldown = 0;
    let resendInterval;

    const customSwal = Swal.mixin({
        customClass: {
            confirmButton: "swal-confirm-btn",
            cancelButton: "swal-cancel-btn",
            popup: "swal-popup",
        },
        buttonsStyling: false,
    });

    initializeOTPInputs();
    startTimer();
    showInitialMessage();

    // Check for flash messages passed via config
    if (config.error) {
        setTimeout(() => {
            customSwal
                .fire({
                    icon: "error",
                    title: "❌ Error de Verificación",
                    text: config.error,
                    confirmButtonText: "Entendido",
                    background: "#1f2937",
                    color: "#fff",
                })
                .then(() => {
                    document.querySelectorAll(".otp-digit").forEach((input) => {
                        input.value = "";
                        input.classList.remove("filled");
                        input.classList.add("error");
                        setTimeout(() => input.classList.remove("error"), 500);
                    });
                    document.getElementById("otp_code").value = "";
                    const firstDigit = document.getElementById("digit-1");
                    if (firstDigit) firstDigit.focus();
                });
        }, 500);
    }

    if (config.success) {
        customSwal.fire({
            icon: "success",
            title: "✅ ¡Verificación Exitosa!",
            text: config.success,
            timer: 2000,
            background: "#1f2937",
            color: "#fff",
        });
    }

    function initializeOTPInputs() {
        const inputs = document.querySelectorAll(".otp-digit");

        inputs.forEach((input, index) => {
            input.addEventListener("input", (e) => {
                const value = e.target.value.replace(/[^0-9]/g, "");
                e.target.value = value;

                if (value) {
                    e.target.classList.add("filled");
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                } else {
                    e.target.classList.remove("filled");
                }

                updateOTPCode();
                validateForm();
            });

            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }

                if (e.key === "Enter") {
                    e.preventDefault();
                    if (isFormValid()) {
                        submitForm();
                    }
                }
            });

            input.addEventListener("paste", (e) => {
                e.preventDefault();
                const pasteData = (
                    e.clipboardData || window.clipboardData
                ).getData("text");
                const digits = pasteData.replace(/[^0-9]/g, "").substr(0, 6);

                if (digits.length === 6) {
                    inputs.forEach((inp, idx) => {
                        if (idx < digits.length) {
                            inp.value = digits[idx];
                            inp.classList.add("filled");
                        }
                    });
                    updateOTPCode();
                    validateForm();
                    inputs[5].focus();
                }
            });
        });

        if (inputs.length > 0) inputs[0].focus();
    }

    function updateOTPCode() {
        const inputs = document.querySelectorAll(".otp-digit");
        let code = "";
        inputs.forEach((input) => (code += input.value));
        const otpCodeInput = document.getElementById("otp_code");
        if (otpCodeInput) otpCodeInput.value = code;
    }

    function isFormValid() {
        const otpCodeInput = document.getElementById("otp_code");
        if (!otpCodeInput) return false;
        const code = otpCodeInput.value;
        return code.length === 6 && /^\d{6}$/.test(code);
    }

    function validateForm() {
        const verifyBtn = document.getElementById("verifyBtn");
        if (verifyBtn) {
            const isValid = isFormValid();
            verifyBtn.disabled = !isValid || timeLeft <= 0;
        }
    }

    function submitForm() {
        if (!isFormValid() || timeLeft <= 0) return;

        const verifyBtn = document.getElementById("verifyBtn");
        const loading = document.getElementById("loading");
        const otpForm = document.getElementById("otpForm");

        if (verifyBtn) verifyBtn.style.display = "none";
        if (loading) loading.style.display = "flex";

        if (otpForm) otpForm.submit();
    }

    function startTimer() {
        const timerDisplay = document.getElementById("timer");
        if (!timerDisplay) return;

        timerInterval = setInterval(() => {
            timeLeft--;

            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const display = `${minutes.toString().padStart(2, "0")}:${seconds
                .toString()
                .padStart(2, "0")}`;

            timerDisplay.textContent = display;

            if (timeLeft <= 10) {
                timerDisplay.classList.add("timer-expired");
            }

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                handleTimerExpired();
            }

            validateForm();
        }, 1000);
    }

    function handleTimerExpired() {
        const timerDisplay = document.getElementById("timer");
        if (timerDisplay) timerDisplay.textContent = "00:00";

        const verifyBtn = document.getElementById("verifyBtn");
        if (verifyBtn) verifyBtn.disabled = true;

        document.querySelectorAll(".otp-digit").forEach((input) => {
            input.disabled = true;
            input.classList.add("error");
        });

        customSwal
            .fire({
                icon: "error",
                title: "⏰ Código Expirado",
                text: "El código de verificación ha expirado. Debes solicitar uno nuevo.",
                confirmButtonText: "Solicitar Nuevo Código",
                background: "#1f2937",
                color: "#fff",
            })
            .then(() => {
                resendCode();
            });
    }

    window.resendCode = function () {
        if (resendCooldown > 0) return;

        customSwal.fire({
            title: "Enviando código...",
            text: "Por favor espera mientras generamos un nuevo código",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        fetch(resendRoute, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({
                email: email,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    clearInterval(timerInterval);
                    timeLeft = TIMER_DURATION;
                    const timerDisplay = document.getElementById("timer");
                    if (timerDisplay)
                        timerDisplay.classList.remove("timer-expired");

                    document.querySelectorAll(".otp-digit").forEach((input) => {
                        input.disabled = false;
                        input.classList.remove("error");
                        input.value = "";
                        input.classList.remove("filled");
                    });

                    const otpCodeInput = document.getElementById("otp_code");
                    if (otpCodeInput) otpCodeInput.value = "";

                    const verifyBtn = document.getElementById("verifyBtn");
                    const loading = document.getElementById("loading");

                    if (verifyBtn) {
                        verifyBtn.disabled = true;
                        verifyBtn.style.display = "block";
                    }
                    if (loading) loading.style.display = "none";

                    startTimer();
                    startResendCooldown();

                    customSwal.fire({
                        icon: "success",
                        title: "📧 Código Enviado",
                        text: "Te hemos enviado un nuevo código de verificación a tu email.",
                        timer: 3000,
                        background: "#1f2937",
                        color: "#fff",
                    });

                    const firstDigit = document.getElementById("digit-1");
                    if (firstDigit) firstDigit.focus();
                } else {
                    customSwal.fire({
                        icon: "error",
                        title: "Error",
                        text:
                            data.message ||
                            "No se pudo enviar el código. Intenta de nuevo.",
                        background: "#1f2937",
                        color: "#fff",
                    });
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                customSwal.fire({
                    icon: "error",
                    title: "Error de Conexión",
                    text: "Hubo un problema al enviar el código. Verifica tu conexión.",
                    background: "#1f2937",
                    color: "#fff",
                });
            });
    };

    function startResendCooldown() {
        resendCooldown = RESEND_COOLDOWN;
        const resendLink = document.getElementById("resendLink");
        const resendTimer = document.getElementById("resendTimer");
        const countdown = document.getElementById("resendCountdown");

        if (resendLink) resendLink.style.display = "none";
        if (resendTimer) resendTimer.style.display = "block";

        resendInterval = setInterval(() => {
            resendCooldown--;
            if (countdown) countdown.textContent = resendCooldown;

            if (resendCooldown <= 0) {
                clearInterval(resendInterval);
                if (resendLink) resendLink.style.display = "inline-block";
                if (resendTimer) resendTimer.style.display = "none";
            }
        }, 1000);
    }

    function showInitialMessage() {
        customSwal.fire({
            icon: "info",
            title: "🔐 Verificación Requerida",
            text: "Te hemos enviado un código de 6 dígitos a tu email. Tienes 60 segundos para ingresarlo.",
            timer: 4000,
            timerProgressBar: true,
            background: "#1f2937",
            color: "#fff",
            showConfirmButton: false,
        });
    }

    const otpForm = document.getElementById("otpForm");
    if (otpForm) {
        otpForm.addEventListener("submit", function (e) {
            e.preventDefault();
            submitForm();
        });
    }

    // Prevenir clic derecho y selección
    document.addEventListener("contextmenu", (e) => e.preventDefault());
    document.addEventListener("selectstart", (e) => e.preventDefault());

    console.log("🔐 OTP Verification page loaded");
});
