document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // SINCRONIZACIÓN DE TEMA CON SIDEBAR
    // ============================================
    function syncFooterTheme() {
        const savedTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
        if (savedTheme === 'dark') {
            document.body.classList.add('ithr-dark-mode');
        } else {
            document.body.classList.remove('ithr-dark-mode');
        }
    }

    // Sincronizar tema al cargar
    syncFooterTheme();

    // Escuchar cambios de tema desde el sidebar
    document.addEventListener('themeChanged', function() {
        syncFooterTheme();
    });

    // Monitorear cambios en localStorage
    window.addEventListener('storage', function(e) {
        if (e.key === 'ithrGlobalTheme') {
            syncFooterTheme();
        }
    });

    // ============================================
    // EFECTOS INTERACTIVOS ORIGINALES
    // ============================================

    // Función para crear partículas flotantes
    function createFooterParticle() {
        const particle = document.createElement('div');
        particle.style.position = 'absolute';
        particle.style.width = Math.random() * 3 + 1 + 'px';
        particle.style.height = particle.style.width;
        particle.style.background = 'rgba(220, 38, 38, 0.4)';
        particle.style.borderRadius = '50%';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = '100%';
        particle.style.pointerEvents = 'none';
        particle.style.animation = `footerParticleFloat ${Math.random() * 4 + 3}s linear forwards`;

        const footerBg = document.querySelector('.footer-bg-animation');
        if (footerBg) {
            footerBg.appendChild(particle);
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.remove();
                }
            }, 7000);
        }
    }

    // Crear partículas cada 4 segundos
    setInterval(createFooterParticle, 4000);

    // Crear estilos de animación para partículas
    const footerStyle = document.createElement('style');
    footerStyle.textContent = `
        @keyframes footerParticleFloat {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-250px) rotate(360deg);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(footerStyle);

    // Efectos hover interactivos
    const footerLinks = document.querySelectorAll('.footer-nav-link, .contact-item');
    footerLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1.2) rotate(10deg)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });

        link.addEventListener('mouseleave', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // Efectos para enlaces sociales
    const socialLinks = document.querySelectorAll('.social-link');
    socialLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) translateY(-2px)';
            this.style.transition = 'all 0.3s ease';
        });

        link.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) translateY(0px)';
        });
    });

    console.log('Footer component initialized with theme sync');
});
