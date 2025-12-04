document.addEventListener('DOMContentLoaded', function() {

    // ============================================================================
    // FUNCIONALIDAD DEL TOGGLE DE TEMA
    // ============================================================================
    const themeToggle = document.getElementById('themeToggle');
    const themeLabel = document.querySelector('.theme-label');
    const themeIcon = document.querySelector('.theme-icon');

    // Cargar tema guardado
    const savedTheme = localStorage.getItem('techHomeTheme') || 'light';
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        if (themeToggle) themeToggle.checked = true;
        updateThemeUI(true);
    } else {
        updateThemeUI(false);
    }

    // Manejar cambio de tema
    if (themeToggle) {
        themeToggle.addEventListener('change', function() {
            if (this.checked) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('techHomeTheme', 'dark');
                updateThemeUI(true);
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('techHomeTheme', 'light');
                updateThemeUI(false);
            }
            
            // Disparar evento personalizado para otros componentes
            document.dispatchEvent(new CustomEvent('themeChanged', {
                detail: { theme: this.checked ? 'dark' : 'light' }
            }));
        });
    }

    function updateThemeUI(isDark) {
        if (themeLabel) {
            themeLabel.textContent = isDark ? 'Claro' : 'Oscuro';
        }
        if (themeIcon) {
            themeIcon.className = isDark ? 'fas fa-sun theme-icon' : 'fas fa-moon theme-icon';
        }
    }

    // ============================================================================
    // EFECTOS INTERACTIVOS DEL SIDEBAR
    // ============================================================================

    // Crear partículas flotantes
    function createNavigationParticle() {
        const particle = document.createElement('div');
        particle.style.cssText = `
            position: absolute;
            width: ${Math.random() * 3 + 1}px;
            height: ${Math.random() * 3 + 1}px;
            background: rgba(220, 38, 38, 0.4);
            border-radius: 50%;
            left: ${Math.random() * 100}%;
            top: 100%;
            pointer-events: none;
            z-index: 1;
            animation: particleFloat ${Math.random() * 6 + 4}s linear forwards;
        `;

        const backgroundElement = document.querySelector('.sidebar-background');
        if (backgroundElement) {
            backgroundElement.appendChild(particle);
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.remove();
                }
            }, 10000);
        }
    }

    // Agregar estilos CSS para la animación de partículas
    if (!document.getElementById('sidebar-particle-animations')) {
        const styleSheet = document.createElement('style');
        styleSheet.id = 'sidebar-particle-animations';
        styleSheet.textContent = `
            @keyframes particleFloat {
                0% {
                    transform: translateY(0) rotate(0deg);
                    opacity: 0.6;
                }
                50% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100vh) rotate(360deg);
                    opacity: 0;
                }
            }
            
            @keyframes hoverParticle {
                0% {
                    transform: scale(0) translateX(0);
                    opacity: 1;
                }
                50% {
                    transform: scale(1) translateX(10px);
                    opacity: 0.8;
                }
                100% {
                    transform: scale(0) translateX(20px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(styleSheet);
    }

    // Crear partícula cada 3 segundos
    setInterval(createNavigationParticle, 3000);

    // ============================================================================
    // EFECTOS HOVER Y NAVEGACIÓN
    // ============================================================================

    // Efectos hover para enlaces de navegación
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach((link, index) => {
        // Animación escalonada de entrada
        link.style.animationDelay = (index * 0.1) + 's';
        
        link.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.nav-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
                icon.style.transition = 'transform 0.3s ease';
            }
            
            // Crear partícula en hover
            createHoverParticle(this);
        });

        link.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.nav-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });

        // Marcar como activo el enlace actual
        if (link.href && window.location.href.includes(link.getAttribute('href'))) {
            link.parentElement.classList.add('active');
        }
    });

    // Crear partícula en hover
    function createHoverParticle(element) {
        const rect = element.getBoundingClientRect();
        const particle = document.createElement('div');
        particle.style.cssText = `
            position: fixed;
            width: 4px;
            height: 4px;
            background: rgba(220, 38, 38, 0.8);
            border-radius: 50%;
            left: ${rect.right - 10}px;
            top: ${rect.top + rect.height / 2}px;
            pointer-events: none;
            z-index: 9999;
            animation: hoverParticle 0.6s ease-out forwards;
        `;

        document.body.appendChild(particle);
        
        setTimeout(() => {
            if (particle.parentNode) {
                particle.remove();
            }
        }, 600);
    }

    // ============================================================================
    // NAVEGACIÓN ACTIVA DINÁMICA
    // ============================================================================
    
    function updateActiveNavigation() {
        const currentPath = window.location.pathname;
        const navItems = document.querySelectorAll('.nav-item');
        
        navItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            if (link) {
                const href = link.getAttribute('href');
                if (href && (currentPath === href || currentPath.startsWith(href + '/'))) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            }
        });
    }

    // Actualizar navegación activa al cargar
    updateActiveNavigation();

    // ============================================================================
    // RESPONSIVE - MENÚ MÓVIL
    // ============================================================================
    
    function createMobileToggle() {
        if (window.innerWidth <= 768) {
            let mobileToggle = document.getElementById('mobile-sidebar-toggle');
            if (!mobileToggle) {
                mobileToggle = document.createElement('button');
                mobileToggle.id = 'mobile-sidebar-toggle';
                mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
                mobileToggle.style.cssText = `
                    position: fixed;
                    top: 20px;
                    left: 20px;
                    z-index: 2001;
                    background: linear-gradient(135deg, #dc2626, #ef4444);
                    color: white;
                    border: none;
                    width: 50px;
                    height: 50px;
                    border-radius: 12px;
                    font-size: 16px;
                    cursor: pointer;
                    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
                    backdrop-filter: blur(10px);
                    transition: all 0.3s ease;
                `;
                document.body.appendChild(mobileToggle);
                
                mobileToggle.addEventListener('click', function() {
                    const sidebar = document.querySelector('.tech-sidebar');
                    if (sidebar) {
                        sidebar.classList.toggle('mobile-open');
                        
                        // Cambiar ícono
                        const icon = this.querySelector('i');
                        if (sidebar.classList.contains('mobile-open')) {
                            icon.className = 'fas fa-times';
                            this.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                        } else {
                            icon.className = 'fas fa-bars';
                            this.style.background = 'linear-gradient(135deg, #dc2626, #ef4444)';
                        }
                    }
                });

                // Efectos hover para el botón móvil
                mobileToggle.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.05)';
                    this.style.boxShadow = '0 6px 20px rgba(220, 38, 38, 0.4)';
                });

                mobileToggle.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                    this.style.boxShadow = '0 4px 15px rgba(220, 38, 38, 0.3)';
                });
            }
        } else {
            // Remover toggle móvil en pantallas grandes
            const mobileToggle = document.getElementById('mobile-sidebar-toggle');
            if (mobileToggle) {
                mobileToggle.remove();
            }
        }
    }

    // Crear toggle móvil si es necesario
    createMobileToggle();
    
    // Recrear en redimensionamiento
    window.addEventListener('resize', createMobileToggle);

    // Cerrar sidebar móvil al hacer clic fuera
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.tech-sidebar');
        const mobileToggle = document.getElementById('mobile-sidebar-toggle');
        
        if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('mobile-open')) {
            if (!sidebar.contains(e.target) && e.target !== mobileToggle && !mobileToggle?.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
                if (mobileToggle) {
                    const icon = mobileToggle.querySelector('i');
                    if (icon) icon.className = 'fas fa-bars';
                    mobileToggle.style.background = 'linear-gradient(135deg, #dc2626, #ef4444)';
                }
            }
        }
    });

    // ============================================================================
    // EFECTOS ADICIONALES
    // ============================================================================

    // Efecto de pulsación en contadores
    const counters = document.querySelectorAll('.nav-counter');
    counters.forEach(counter => {
        counter.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.transition = 'transform 0.2s ease';
        });

        counter.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Efecto en el botón del sitio web
    const websiteButton = document.querySelector('.website-link');
    if (websiteButton) {
        websiteButton.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        });

        websiteButton.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    }

    // ============================================================================
    // ACTUALIZACIÓN DE CONTADORES DINÁMICOS
    // ============================================================================

    function updateCounters() {
        // Esta función puede ser llamada desde otros scripts
        // para actualizar los contadores dinámicamente
        const counters = document.querySelectorAll('.nav-counter');
        counters.forEach(counter => {
            // Efecto de actualización
            counter.style.transform = 'scale(1.2)';
            setTimeout(() => {
                counter.style.transform = 'scale(1)';
            }, 200);
        });
    }

    // Exponer función para uso externo
    window.updateSidebarCounters = updateCounters;

    // ============================================================================
    // INICIALIZACIÓN FINAL
    // ============================================================================
    
    console.log('✅ Sidebar Tech Home inicializado correctamente');
});

// ============================================================================
// API PÚBLICA DEL SIDEBAR
// ============================================================================
window.TechSidebar = {
    updateCounters: function() {
        if (typeof window.updateSidebarCounters === 'function') {
            window.updateSidebarCounters();
        }
    },
    
    setActiveNavigation: function(route) {
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            if (link && link.getAttribute('href') === route) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    },
    
    toggleMobile: function() {
        const sidebar = document.querySelector('.tech-sidebar');
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
        }
    },
    
    changeTheme: function(isDark) {
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.checked = isDark;
            themeToggle.dispatchEvent(new Event('change'));
        }
    }
};