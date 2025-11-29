/**
 * ============================================
 * FUNCIONALIDADES JAVASCRIPT PARA SIDEBAR
 * ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
});

/**
 * Inicializar sidebar
 */
function initSidebar() {
    // Configurar toggle de tema
    setupThemeToggle();
    
    // Configurar efectos interactivos
    setupInteractiveEffects();
    
    // Crear partículas flotantes
    createNavigationParticles();
    
    // Configurar enlaces activos
    setupActiveLinks();
    
    console.log('🎛️ Sidebar initialized successfully');
}

/**
 * Configurar toggle de tema
 */
function setupThemeToggle() {
    const themeToggle = document.getElementById('ithrThemeToggle');
    const themeLabel = document.querySelector('.ithr-theme-label');
    const themeDescription = document.querySelector('.ithr-theme-description');

    if (!themeToggle || !themeLabel || !themeDescription) return;

    // Cargar tema guardado
    const savedTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
    if (savedTheme === 'dark') {
        themeToggle.checked = true;
        themeLabel.textContent = 'Modo Claro';
        themeDescription.textContent = 'Cambiar a claro';
        document.body.classList.add('ithr-dark-mode');
    } else {
        themeLabel.textContent = 'Modo Oscuro';
        themeDescription.textContent = 'Cambiar a oscuro';
        document.body.classList.remove('ithr-dark-mode');
    }

    // Manejar cambio de tema
    themeToggle.addEventListener('change', function() {
        if (this.checked) {
            // Activar modo oscuro
            document.body.classList.add('ithr-dark-mode');
            themeLabel.textContent = 'Modo Claro';
            themeDescription.textContent = 'Cambiar a claro';
            localStorage.setItem('ithrGlobalTheme', 'dark');
        } else {
            // Activar modo claro
            document.body.classList.remove('ithr-dark-mode');
            themeLabel.textContent = 'Modo Oscuro';
            themeDescription.textContent = 'Cambiar a oscuro';
            localStorage.setItem('ithrGlobalTheme', 'light');
        }
        
        // Notificar cambio de tema para sincronizar otros componentes
        document.dispatchEvent(new Event('themeChanged'));
    });
}

/**
 * Configurar efectos interactivos
 */
function setupInteractiveEffects() {
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
}

/**
 * Crear partículas flotantes
 */
function createNavigationParticles() {
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
        particle.style.animation = `particleFloat ${Math.random() * 6 + 4}s linear forwards`;

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
    
    // Crear estilos de animación para partículas si no existen
    if (!document.getElementById('particleStyles')) {
        const style = document.createElement('style');
        style.id = 'particleStyles';
        style.textContent = `
            @keyframes particleFloat {
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
        document.head.appendChild(style);
    }
}

/**
 * Configurar enlaces activos
 */
function setupActiveLinks() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.ithr-nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        
        // Remover clase activa de todos los enlaces
        const navItem = link.closest('.ithr-nav-item');
        if (navItem) {
            navItem.classList.remove('ithr-active');
        }
        
        // Agregar clase activa al enlace actual
        if (href && (currentPath === href || currentPath.startsWith(href + '/'))) {
            if (navItem) {
                navItem.classList.add('ithr-active');
            }
        }
    });
}

/**
 * Actualizar badges de navegación
 */
function updateNavBadges() {
    // Esta función puede ser llamada desde otros scripts para actualizar
    // los contadores dinámicamente
    const badges = document.querySelectorAll('.ithr-nav-badge');
    
    badges.forEach(badge => {
        const link = badge.closest('.ithr-nav-link');
        const href = link?.getAttribute('href');
        
        // Aquí se pueden agregar llamadas AJAX para obtener contadores actualizados
        // Por ejemplo:
        if (href && href.includes('estudiantes')) {
            // fetchEstudiantesCount().then(count => badge.textContent = count);
        }
    });
}

/**
 * Sincronizar tema con otros componentes
 */
function syncThemeWithComponents() {
    const savedTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
    
    if (savedTheme === 'dark') {
        document.body.classList.add('ithr-dark-mode');
    } else {
        document.body.classList.remove('ithr-dark-mode');
    }
    
    // Notificar a otros componentes
    document.dispatchEvent(new Event('themeChanged'));
}

/**
 * Escuchar cambios de tema desde otros componentes
 */
document.addEventListener('themeChanged', function() {
    const themeToggle = document.getElementById('ithrThemeToggle');
    const savedTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
    
    if (themeToggle) {
        themeToggle.checked = savedTheme === 'dark';
    }
});

/**
 * API pública para el sidebar
 */
window.SidebarAPI = {
    updateBadges: updateNavBadges,
    syncTheme: syncThemeWithComponents,
    setActiveLink: function(href) {
        const navLinks = document.querySelectorAll('.ithr-nav-link');
        navLinks.forEach(link => {
            const navItem = link.closest('.ithr-nav-item');
            if (navItem) {
                navItem.classList.remove('ithr-active');
            }
            
            if (link.getAttribute('href') === href) {
                if (navItem) {
                    navItem.classList.add('ithr-active');
                }
            }
        });
    }
};

/**
 * Función para actualizar contadores específicos
 */
function updateSpecificCounter(selector, count) {
    const badge = document.querySelector(selector);
    if (badge) {
        badge.textContent = count;
        
        // Animación de actualización
        badge.style.transform = 'scale(1.2)';
        badge.style.transition = 'transform 0.2s ease';
        
        setTimeout(() => {
            badge.style.transform = 'scale(1)';
        }, 200);
    }
}

// Exponer función para uso externo
window.updateSidebarCounter = updateSpecificCounter;