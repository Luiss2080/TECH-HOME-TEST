// ============================================
// WELCOME DASHBOARD - TECH HOME
// Instituto de Robótica y Tecnología
// ============================================

class WelcomeDashboard {
    constructor() {
        this.animationDelay = 100;
        this.countUpDuration = 2000;
        this.init();
    }

    // ============================================
    // INICIALIZACIÓN
    // ============================================
    init() {
        this.updateDateTime();
        this.startDateTimeUpdater();
        this.initCardInteractions();
        this.initCountUpAnimations();
        this.initQuickActions();
        this.initKeyboardNavigation();
        this.initIntersectionObserver();
        this.startBackgroundEffects();
        this.syncThemeWithSystem();
        this.initPreferencesPersistence();

        console.log('✅ Welcome Dashboard inicializado correctamente');
    }

    // ============================================
    // FECHA Y HORA EN TIEMPO REAL
    // ============================================
    updateDateTime() {
        const now = new Date();
        
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };

        const dateOptions = {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };

        const formattedTime = now.toLocaleTimeString('es-ES', timeOptions);
        const formattedDate = now.toLocaleDateString('es-ES', dateOptions);

        // Capitalizar primera letra del día
        const capitalizedDate = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);

        const timeElement = document.getElementById('welcome-current-time');
        const dateElements = document.querySelectorAll('.current-date');

        if (timeElement) {
            timeElement.textContent = formattedTime;
        }

        dateElements.forEach(element => {
            element.textContent = capitalizedDate;
        });
    }

    startDateTimeUpdater() {
        setInterval(() => this.updateDateTime(), 1000);
    }

    // ============================================
    // INTERACCIONES DE TARJETAS
    // ============================================
    initCardInteractions() {
        const cards = document.querySelectorAll('.dashboard-card');
        
        cards.forEach((card, index) => {
            // Animación de entrada escalonada
            card.style.animationDelay = `${0.4 + (index * 0.1)}s`;
            
            // Event listeners para interacciones
            card.addEventListener('mouseenter', (e) => this.handleCardHover(e, true));
            card.addEventListener('mouseleave', (e) => this.handleCardHover(e, false));
            card.addEventListener('click', (e) => this.handleCardClick(e));
            
            // Efectos de focus para accesibilidad
            card.addEventListener('focus', (e) => this.handleCardFocus(e, true));
            card.addEventListener('blur', (e) => this.handleCardFocus(e, false));
        });
    }

    handleCardHover(event, isHovering) {
        const card = event.currentTarget;
        const icon = card.querySelector('.card-icon');
        const arrow = card.querySelector('.card-arrow');
        
        if (isHovering) {
            card.style.zIndex = '10';
            
            // Efecto de elevación adicional
            setTimeout(() => {
                if (card.matches(':hover')) {
                    card.style.transform = 'translateY(-6px) scale(1.02)';
                }
            }, 50);
            
            // Animación del icono
            if (icon) {
                icon.style.transform = 'scale(1.15) rotate(5deg)';
            }
            
        } else {
            card.style.zIndex = '';
            card.style.transform = '';
            
            if (icon) {
                icon.style.transform = '';
            }
        }
    }

    handleCardClick(event) {
        const card = event.currentTarget;
        const module = card.getAttribute('data-module');
        
        // Efecto visual de click
        card.style.transform = 'translateY(-4px) scale(0.98)';
        
        setTimeout(() => {
            card.style.transform = '';
        }, 150);

        // Ripple effect
        this.createRippleEffect(event);
        
        // Navegación basada en el módulo
        this.navigateToModule(module);
    }

    handleCardFocus(event, isFocused) {
        const card = event.currentTarget;
        
        if (isFocused) {
            card.style.outline = '2px solid var(--welcome-primary)';
            card.style.outlineOffset = '2px';
        } else {
            card.style.outline = '';
            card.style.outlineOffset = '';
        }
    }

    createRippleEffect(event) {
        const card = event.currentTarget;
        const rect = card.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        const ripple = document.createElement('div');
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(220, 38, 38, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
            z-index: 100;
        `;
        
        card.style.position = 'relative';
        card.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    navigateToModule(module) {
        // Mapeo de módulos a rutas
        const moduleRoutes = {
            'biblioteca': '#biblioteca',
            'componentes': '#componentes', 
            'cursos': '#cursos',
            'proyectos': '#proyectos',
            'laboratorios': '#laboratorios',
            'estudiantes': '#estudiantes'
        };

        const route = moduleRoutes[module];
        
        if (route) {
            console.log(`Navegando a módulo: ${module}`);
            // Aquí podrías implementar navegación real
            // window.location.href = route;
            
            // Por ahora, mostrar notificación
            this.showNotification(`Accediendo a ${module}...`, 'info');
        }
    }

    // ============================================
    // ANIMACIONES DE CONTADORES
    // ============================================
    initCountUpAnimations() {
        const numbers = document.querySelectorAll('.stat-number');
        
        numbers.forEach(number => {
            const target = parseInt(number.getAttribute('data-target'));
            if (target) {
                this.observeElementForCountUp(number, target);
            }
        });
    }

    observeElementForCountUp(element, target) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !element.hasAttribute('data-counted')) {
                    this.animateCountUp(element, target);
                    element.setAttribute('data-counted', 'true');
                }
            });
        }, { threshold: 0.5 });

        observer.observe(element);
    }

    animateCountUp(element, target) {
        const duration = this.countUpDuration;
        const start = 0;
        const startTime = performance.now();
        
        // Añadir clase de animación
        element.style.animation = 'countUp 0.8s ease-out';
        
        const updateCount = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Usar easing function para suavizar
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(start + (target - start) * easeOut);
            
            element.textContent = this.formatNumber(current);
            
            if (progress < 1) {
                requestAnimationFrame(updateCount);
            } else {
                element.textContent = this.formatNumber(target);
                
                // Efecto final
                element.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    element.style.transform = '';
                }, 200);
            }
        };
        
        requestAnimationFrame(updateCount);
    }

    formatNumber(num) {
        if (num >= 1000) {
            return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
        }
        return num.toString();
    }

    // ============================================
    // ACCIONES RÁPIDAS
    // ============================================
    initQuickActions() {
        const quickActions = document.querySelectorAll('.quick-action-item');
        
        quickActions.forEach((action, index) => {
            action.style.animationDelay = `${0.6 + (index * 0.1)}s`;
            
            action.addEventListener('mouseenter', (e) => this.handleActionHover(e, true));
            action.addEventListener('mouseleave', (e) => this.handleActionHover(e, false));
            action.addEventListener('click', (e) => this.handleActionClick(e));
        });
    }

    handleActionHover(event, isHovering) {
        const action = event.currentTarget;
        const icon = action.querySelector('.action-icon');
        
        if (isHovering) {
            action.style.transform = 'translateY(-3px) scale(1.02)';
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(-5deg)';
            }
        } else {
            action.style.transform = '';
            if (icon) {
                icon.style.transform = '';
            }
        }
    }

    handleActionClick(event) {
        event.preventDefault();
        const action = event.currentTarget;
        const href = action.getAttribute('href');
        const label = action.querySelector('.action-label').textContent;
        
        // Efecto visual
        action.style.transform = 'translateY(-1px) scale(0.98)';
        setTimeout(() => {
            action.style.transform = '';
        }, 150);

        // Procesar acción
        if (href && href !== '#') {
            setTimeout(() => {
                window.location.href = href;
            }, 200);
        } else {
            this.showNotification(`${label} próximamente disponible`, 'info');
        }
    }

    // ============================================
    // NAVEGACIÓN CON TECLADO
    // ============================================
    initKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            this.handleKeyboardNavigation(e);
        });
    }

    handleKeyboardNavigation(event) {
        const cards = Array.from(document.querySelectorAll('.dashboard-card, .quick-action-item'));
        const currentFocus = document.activeElement;
        const currentIndex = cards.indexOf(currentFocus);
        
        let nextIndex = -1;
        
        switch (event.key) {
            case 'ArrowDown':
            case 'ArrowRight':
                event.preventDefault();
                nextIndex = (currentIndex + 1) % cards.length;
                break;
                
            case 'ArrowUp':
            case 'ArrowLeft':
                event.preventDefault();
                nextIndex = currentIndex <= 0 ? cards.length - 1 : currentIndex - 1;
                break;
                
            case 'Enter':
            case ' ':
                if (currentFocus && cards.includes(currentFocus)) {
                    event.preventDefault();
                    currentFocus.click();
                }
                break;
        }
        
        if (nextIndex >= 0 && cards[nextIndex]) {
            cards[nextIndex].focus();
        }
    }

    // ============================================
    // OBSERVADOR DE INTERSECCIÓN
    // ============================================
    initIntersectionObserver() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '50px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    this.triggerElementAnimation(entry.target);
                }
            });
        }, observerOptions);

        // Observar elementos animables
        const animatableElements = document.querySelectorAll(
            '.dashboard-card, .quick-action-item, .hero-section, .section-header'
        );
        
        animatableElements.forEach(element => {
            observer.observe(element);
        });
    }

    triggerElementAnimation(element) {
        if (element.classList.contains('dashboard-card')) {
            element.style.transform = 'translateY(0)';
            element.style.opacity = '1';
        }
    }

    // ============================================
    // EFECTOS DE FONDO
    // ============================================
    startBackgroundEffects() {
        this.createFloatingParticles();
        this.startParticleAnimation();
    }

    createFloatingParticles() {
        const dashboard = document.querySelector('.welcome-dashboard');
        if (!dashboard) return;

        for (let i = 0; i < 15; i++) {
            const particle = document.createElement('div');
            particle.className = 'floating-particle';
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 4 + 2}px;
                height: ${Math.random() * 4 + 2}px;
                background: rgba(220, 38, 38, ${Math.random() * 0.3 + 0.1});
                border-radius: 50%;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                pointer-events: none;
                z-index: 1;
                animation: float ${Math.random() * 20 + 10}s infinite linear;
            `;
            
            dashboard.appendChild(particle);
        }
    }

    startParticleAnimation() {
        if (!document.getElementById('particle-animations')) {
            const style = document.createElement('style');
            style.id = 'particle-animations';
            style.textContent = `
                @keyframes float {
                    0% { transform: translate(0, 100vh) rotate(0deg); opacity: 0; }
                    10% { opacity: 1; }
                    90% { opacity: 1; }
                    100% { transform: translate(0, -100vh) rotate(360deg); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    // ============================================
    // SINCRONIZACIÓN DE TEMA
    // ============================================
    syncThemeWithSystem() {
        const savedTheme = localStorage.getItem('techHomeTheme') || 'light';
        this.applyTheme(savedTheme);
        
        // Escuchar cambios de tema
        document.addEventListener('themeChanged', (e) => {
            this.applyTheme(e.detail.theme);
        });
        
        // Monitorear cambios del sistema
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addListener((e) => {
                if (!localStorage.getItem('techHomeTheme')) {
                    this.applyTheme(e.matches ? 'dark' : 'light');
                }
            });
        }
    }

    applyTheme(theme) {
        document.body.setAttribute('data-theme', theme);
        
        // Actualizar meta theme-color
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', theme === 'dark' ? '#0f172a' : '#f8fafc');
        }
    }

    // ============================================
    // PERSISTENCIA DE PREFERENCIAS
    // ============================================
    initPreferencesPersistence() {
        // Guardar scroll position
        window.addEventListener('beforeunload', () => {
            localStorage.setItem('welcomeScrollPosition', window.scrollY.toString());
        });

        // Restaurar scroll position
        const savedScrollPosition = localStorage.getItem('welcomeScrollPosition');
        if (savedScrollPosition) {
            setTimeout(() => {
                window.scrollTo(0, parseInt(savedScrollPosition));
                localStorage.removeItem('welcomeScrollPosition');
            }, 100);
        }
    }

    // ============================================
    // UTILIDADES
    // ============================================
    showNotification(message, type = 'info') {
        // Crear notificación temporal
        const notification = document.createElement('div');
        notification.className = `welcome-notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: var(--welcome-primary);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
            z-index: 1000;
            animation: slideInFromRight 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutToRight 0.3s ease-in forwards';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // ============================================
    // API PÚBLICA
    // ============================================
    updateStats(stats) {
        Object.keys(stats).forEach(key => {
            const element = document.querySelector(`[data-target="${stats[key]}"]`);
            if (element) {
                element.setAttribute('data-target', stats[key]);
                this.animateCountUp(element, stats[key]);
            }
        });
    }

    refreshCards() {
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach((card, index) => {
            card.style.animation = 'none';
            setTimeout(() => {
                card.style.animation = '';
                card.style.animationDelay = `${index * 0.1}s`;
            }, 10);
        });
    }

    showMaintenanceMode() {
        const dashboard = document.querySelector('.welcome-dashboard');
        if (dashboard) {
            const overlay = document.createElement('div');
            overlay.innerHTML = `
                <div style="text-align: center; padding: 2rem;">
                    <i class="fas fa-tools" style="font-size: 3rem; color: var(--welcome-primary); margin-bottom: 1rem;"></i>
                    <h3>Modo Mantenimiento</h3>
                    <p>El sistema se encuentra temporalmente en mantenimiento. Volveremos pronto.</p>
                </div>
            `;
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(248, 250, 252, 0.95);
                backdrop-filter: blur(10px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            `;
            document.body.appendChild(overlay);
        }
    }
}

// ============================================
// AGREGAR ESTILOS CSS NECESARIOS
// ============================================
if (!document.getElementById('welcome-dynamic-styles')) {
    const style = document.createElement('style');
    style.id = 'welcome-dynamic-styles';
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        @keyframes slideInFromRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutToRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

// ============================================
// INICIALIZACIÓN AUTOMÁTICA
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Crear instancia del dashboard
    window.welcomeDashboard = new WelcomeDashboard();
});

// ============================================
// API PÚBLICA SIMPLIFICADA
// ============================================
window.WelcomeDashboard = {
    updateStats: (stats) => window.welcomeDashboard?.updateStats(stats),
    refreshCards: () => window.welcomeDashboard?.refreshCards(),
    showNotification: (message, type) => window.welcomeDashboard?.showNotification(message, type),
    showMaintenance: () => window.welcomeDashboard?.showMaintenanceMode()
};