/**
 * TECH HOME - HOME PAGE FUNCTIONALITY
 * ====================================
 * JavaScript para la página principal
 * Incluye: animaciones, newsletter, interactividad
 */

class TechHomePage {
    constructor() {
        this.isLoading = false;
        this.animationObserver = null;
        this.particles = [];
        
        this.init();
    }

    /**
     * Inicialización de la página
     */
    init() {
        this.setupAnimations();
        this.setupNewsletterForm();
        this.setupScrollEffects();
        this.setupParticleSystem();
        this.setupCardInteractions();
        this.setupStatCounters();
        
        // Debug
        console.log('🏠 TECH HOME Page: Initialized successfully');
    }

    /**
     * Configurar animaciones de entrada
     */
    setupAnimations() {
        // Intersection Observer para animaciones
        this.animationObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        
                        // Animaciones especiales por tipo
                        if (entry.target.classList.contains('course-card')) {
                            this.animateCourseCard(entry.target);
                        } else if (entry.target.classList.contains('stat-item')) {
                            this.animateStatItem(entry.target);
                        } else if (entry.target.classList.contains('category-card')) {
                            this.animateCategoryCard(entry.target);
                        }
                    }
                });
            },
            {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        // Observar elementos animables
        const animatableElements = document.querySelectorAll(`
            .course-card, .resource-card, .category-card,
            .stat-item, .section-header, .cta-content
        `);

        animatableElements.forEach(element => {
            this.animationObserver.observe(element);
        });
    }

    /**
     * Animar tarjeta de curso
     */
    animateCourseCard(card) {
        const index = Array.from(card.parentElement.children).indexOf(card);
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    }

    /**
     * Animar elemento de estadística
     */
    animateStatItem(statItem) {
        const numberElement = statItem.querySelector('.stat-number');
        if (!numberElement) return;

        const finalValue = parseInt(numberElement.textContent) || 0;
        let currentValue = 0;
        const increment = finalValue / 50;
        const duration = 2000;
        const stepTime = duration / 50;

        const counter = setInterval(() => {
            currentValue += increment;
            
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(counter);
            }
            
            numberElement.textContent = Math.floor(currentValue);
        }, stepTime);
    }

    /**
     * Animar tarjeta de categoría
     */
    animateCategoryCard(card) {
        const icon = card.querySelector('.category-icon');
        if (icon) {
            setTimeout(() => {
                icon.style.animation = 'categoryIconBounce 0.6s ease-out';
            }, 300);
        }
    }

    /**
     * Configurar formulario de newsletter
     */
    setupNewsletterForm() {
        const newsletterForm = document.getElementById('newsletter-form');
        if (!newsletterForm) return;

        newsletterForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleNewsletterSubmit(e.target);
        });

        // Validación en tiempo real
        const emailInput = newsletterForm.querySelector('input[type="email"]');
        if (emailInput) {
            emailInput.addEventListener('input', (e) => {
                this.validateNewsletterEmail(e.target);
            });
        }
    }

    /**
     * Validar email del newsletter
     */
    validateNewsletterEmail(input) {
        const email = input.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            input.style.borderColor = '#ef4444';
            input.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
        } else {
            input.style.borderColor = '#e5e7eb';
            input.style.boxShadow = 'none';
        }
    }

    /**
     * Manejar envío del newsletter
     */
    async handleNewsletterSubmit(form) {
        if (this.isLoading) return;

        const emailInput = form.querySelector('input[type="email"]');
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonContent = submitButton.innerHTML;

        try {
            this.isLoading = true;
            
            // Validar email
            const email = emailInput.value.trim();
            if (!this.isValidEmail(email)) {
                this.showToast('Por favor, ingresa un email válido', 'error');
                return;
            }

            // Mostrar loading
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suscribiendo...';

            // Simular llamada a API
            const response = await this.subscribeToNewsletter(email);

            if (response.success) {
                this.showToast('¡Te has suscrito exitosamente al newsletter!', 'success');
                form.reset();
                this.createConfetti();
            } else {
                throw new Error(response.message || 'Error en la suscripción');
            }

        } catch (error) {
            this.showToast('Error al procesar la suscripción. Inténtalo de nuevo.', 'error');
            console.error('Newsletter subscription error:', error);
        } finally {
            this.isLoading = false;
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonContent;
        }
    }

    /**
     * Suscribir al newsletter (simulado)
     */
    async subscribeToNewsletter(email) {
        // Simular llamada AJAX
        return new Promise((resolve) => {
            setTimeout(() => {
                // Simular éxito con 90% de probabilidad
                resolve({
                    success: Math.random() > 0.1,
                    message: 'Suscripción exitosa'
                });
            }, 2000);
        });
    }

    /**
     * Validar formato de email
     */
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Configurar efectos de scroll
     */
    setupScrollEffects() {
        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    this.handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    /**
     * Manejar efectos de scroll
     */
    handleScroll() {
        const scrollY = window.scrollY;
        
        // Parallax en hero
        const heroElements = document.querySelectorAll('.floating-particles, .circuit-pattern');
        heroElements.forEach((element, index) => {
            const speed = 0.5 + (index * 0.2);
            element.style.transform = `translateY(${scrollY * speed}px)`;
        });

        // Efecto de desvanecimiento en stats
        const statsItems = document.querySelectorAll('.stat-item');
        statsItems.forEach(item => {
            const rect = item.getBoundingClientRect();
            const opacity = Math.max(0, Math.min(1, 1 - (rect.top - window.innerHeight + 100) / 200));
            item.style.opacity = opacity;
        });
    }

    /**
     * Sistema de partículas
     */
    setupParticleSystem() {
        const particlesContainer = document.querySelector('.floating-particles');
        if (!particlesContainer) return;

        // Crear partículas
        for (let i = 0; i < 20; i++) {
            this.createParticle(particlesContainer);
        }

        // Animar partículas
        this.animateParticles();
    }

    /**
     * Crear partícula individual
     */
    createParticle(container) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Propiedades aleatorias
        const size = Math.random() * 4 + 2;
        const x = Math.random() * 100;
        const y = Math.random() * 100;
        const animationDuration = Math.random() * 10 + 5;
        const delay = Math.random() * 5;
        
        particle.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            background: #dc2626;
            border-radius: 50%;
            left: ${x}%;
            top: ${y}%;
            opacity: 0;
            animation: particleFloat ${animationDuration}s ${delay}s infinite ease-in-out;
        `;

        container.appendChild(particle);
        this.particles.push({
            element: particle,
            x: x,
            y: y,
            vx: (Math.random() - 0.5) * 0.5,
            vy: (Math.random() - 0.5) * 0.5
        });
    }

    /**
     * Animar partículas
     */
    animateParticles() {
        setInterval(() => {
            this.particles.forEach(particle => {
                particle.x += particle.vx;
                particle.y += particle.vy;
                
                // Rebotar en bordes
                if (particle.x <= 0 || particle.x >= 100) particle.vx *= -1;
                if (particle.y <= 0 || particle.y >= 100) particle.vy *= -1;
                
                // Actualizar posición
                particle.element.style.left = particle.x + '%';
                particle.element.style.top = particle.y + '%';
            });
        }, 50);
    }

    /**
     * Configurar interacciones de tarjetas
     */
    setupCardInteractions() {
        // Efecto de hover en tarjetas de curso
        const courseCards = document.querySelectorAll('.course-card');
        courseCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                this.highlightCard(card);
            });
            
            card.addEventListener('mouseleave', () => {
                this.unhighlightCard(card);
            });
        });

        // Efecto de click en categorías
        const categoryCards = document.querySelectorAll('.category-card');
        categoryCards.forEach(card => {
            card.addEventListener('click', (e) => {
                this.animateCardClick(card);
            });
        });
    }

    /**
     * Resaltar tarjeta
     */
    highlightCard(card) {
        const otherCards = card.parentElement.children;
        Array.from(otherCards).forEach(otherCard => {
            if (otherCard !== card) {
                otherCard.style.opacity = '0.7';
                otherCard.style.transform = 'scale(0.95)';
            }
        });
    }

    /**
     * Des-resaltar tarjeta
     */
    unhighlightCard(card) {
        const otherCards = card.parentElement.children;
        Array.from(otherCards).forEach(otherCard => {
            otherCard.style.opacity = '1';
            otherCard.style.transform = 'scale(1)';
        });
    }

    /**
     * Animar click en tarjeta
     */
    animateCardClick(card) {
        card.style.transform = 'scale(0.95)';
        setTimeout(() => {
            card.style.transform = '';
        }, 150);
    }

    /**
     * Configurar contadores de estadísticas
     */
    setupStatCounters() {
        const statNumbers = document.querySelectorAll('.stat-number');
        
        statNumbers.forEach(stat => {
            // Preparar para animación
            const finalValue = stat.textContent;
            stat.textContent = '0';
            stat.setAttribute('data-final', finalValue);
            
            // El contador se activará cuando el elemento sea visible
            // (manejado en animateStatItem)
        });
    }

    /**
     * Crear efecto de confetti
     */
    createConfetti() {
        const colors = ['#dc2626', '#ef4444', '#f97316', '#eab308'];
        const confettiContainer = document.createElement('div');
        confettiContainer.className = 'confetti-container';
        confettiContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
        `;

        document.body.appendChild(confettiContainer);

        // Crear partículas de confetti
        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.style.cssText = `
                position: absolute;
                width: 6px;
                height: 6px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                left: ${Math.random() * 100}%;
                top: -10px;
                border-radius: 2px;
                animation: confettiFall ${Math.random() * 3 + 2}s linear forwards;
            `;
            
            confettiContainer.appendChild(confetti);
        }

        // Limpiar después de la animación
        setTimeout(() => {
            document.body.removeChild(confettiContainer);
        }, 5000);
    }

    /**
     * Mostrar toast notification
     */
    showToast(message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: document.body.classList.contains('ithr-dark-mode') ? '#374151' : '#ffffff'
            });
        } else {
            // Fallback simple
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#22c55e' : type === 'error' ? '#ef4444' : '#3b82f6'};
                color: white;
                padding: 1rem 2rem;
                border-radius: 8px;
                z-index: 1000;
                animation: toastSlide 0.3s ease-out;
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'toastSlide 0.3s ease-out reverse';
                setTimeout(() => {
                    if (toast.parentElement) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }
    }

    /**
     * Cleanup al destruir
     */
    destroy() {
        if (this.animationObserver) {
            this.animationObserver.disconnect();
        }

        // Limpiar partículas
        this.particles.forEach(particle => {
            if (particle.element.parentElement) {
                particle.element.parentElement.removeChild(particle.element);
            }
        });

        console.log('🏠 TECH HOME Page: Destroyed');
    }
}

// Estilos CSS dinámicos para animaciones
const homeAnimationStyles = `
    <style id="home-animation-styles">
        /* Animaciones para partículas */
        @keyframes particleFloat {
            0%, 100% { 
                opacity: 0; 
                transform: scale(0) rotate(0deg); 
            }
            50% { 
                opacity: 0.7; 
                transform: scale(1) rotate(180deg); 
            }
        }

        /* Animación para icono de categoría */
        @keyframes categoryIconBounce {
            0%, 100% { transform: scale(1) rotate(0deg); }
            25% { transform: scale(1.1) rotate(-10deg); }
            75% { transform: scale(1.1) rotate(10deg); }
        }

        /* Animación de confetti */
        @keyframes confettiFall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Animación de toast */
        @keyframes toastSlide {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Estados de animación para elementos */
        .course-card,
        .resource-card,
        .category-card,
        .section-header {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .course-card.animate-in,
        .resource-card.animate-in,
        .category-card.animate-in,
        .section-header.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        /* Hover mejorado para tarjetas */
        .course-card,
        .resource-card {
            will-change: transform, box-shadow;
        }

        /* Partículas flotantes */
        .particle {
            will-change: transform, opacity;
        }

        /* Mejoras de rendimiento */
        .floating-particles,
        .circuit-pattern {
            will-change: transform;
        }

        /* Estados de loading para botones */
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            pointer-events: none;
        }

        .btn .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .course-card,
            .resource-card,
            .category-card {
                opacity: 1;
                transform: translateY(0);
            }
            
            .particle {
                display: none;
            }
        }
    </style>
`;

// Inyectar estilos si no existen
if (!document.querySelector('#home-animation-styles')) {
    document.head.insertAdjacentHTML('beforeend', homeAnimationStyles);
}

// Inicialización automática cuando el DOM esté listo
let techHomePage;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        techHomePage = new TechHomePage();
    });
} else {
    techHomePage = new TechHomePage();
}

// Exposición global para uso externo
window.TechHomePage = TechHomePage;
window.techHomePage = techHomePage;

// Cleanup al cerrar la página
window.addEventListener('beforeunload', () => {
    if (techHomePage) {
        techHomePage.destroy();
    }
});