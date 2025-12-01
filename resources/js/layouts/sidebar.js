document.addEventListener('DOMContentLoaded', function() {

    // ============================================================================
    // TOGGLE MODO OSCURO MEJORADO - AFECTA TODA LA PANTALLA
    // ============================================================================
    const themeToggle = document.getElementById('ithrThemeToggle');
    const themeLabel = document.querySelector('.ithr-theme-label');
    const themeDescription = document.querySelector('.ithr-theme-description');

    // Cargar tema guardado
    const savedTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
    if (savedTheme === 'dark') {
        themeToggle.checked = true;
        themeLabel.textContent = 'Claro';
        themeDescription.textContent = '';
        document.body.classList.add('ithr-dark-mode');
    } else {
        themeLabel.textContent = 'Oscuro';
        themeDescription.textContent = '';
    }

    // Manejar cambio de tema
    themeToggle.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('ithr-dark-mode');
            themeLabel.textContent = 'Oscuro';
            themeDescription.textContent = '';
            localStorage.setItem('ithrGlobalTheme', 'dark');
        } else {
            document.body.classList.remove('ithr-dark-mode');
            themeLabel.textContent = 'Claro';
            themeDescription.textContent = '';
            localStorage.setItem('ithrGlobalTheme', 'light');
        }
    });

    // ============================================================================
    // EFECTOS INTERACTIVOS DEL SIDEBAR
    // ============================================================================

    // Crear partículas flotantes
    function createNavigationParticle() {
        const particle = document.createElement('div');
        particle.style.position = 'absolute';
        particle.style.width = Math.random() * 2 + 1 + 'px';
        particle.style.height = particle.style.width;
        particle.style.background = 'rgba(220, 38, 38, 0.3)';
        particle.style.borderRadius = '50%';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = '100%';
        particle.style.pointerEvents = 'none';
        particle.style.animation = `ithr-particle-float ${Math.random() * 6 + 4}s linear forwards`;

        const backgroundElement = document.querySelector('.ithr-animated-background');
        if (backgroundElement) {
            backgroundElement.appendChild(particle);
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.remove();
                }
            }, 10000);
        }
    }

    // Crear partícula cada 5 segundos
    setInterval(createNavigationParticle, 5000);

    // ============================================================================
    // EFECTOS HOVER Y NAVEGACIÓN
    // ============================================================================

    // Efectos hover para enlaces de navegación
    const navLinks = document.querySelectorAll('.ithr-nav-link');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.ithr-nav-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        link.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.ithr-nav-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // Efecto para la tarjeta del sitio web
    const websiteCard = document.querySelector('.ithr-website-card');
    if (websiteCard) {
        websiteCard.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        });

        websiteCard.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    }

    // Efecto para el control de tema
    const themeControl = document.querySelector('.ithr-theme-control');
    if (themeControl) {
        themeControl.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 6px 20px rgba(220, 38, 38, 0.15)';
        });

        themeControl.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    }
});