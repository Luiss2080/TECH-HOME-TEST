// ============================================
// FOOTER TECH HOME - Instituto de Robótica
// ============================================

class TechFooterComponent {
    constructor() {
        this.scrollToTopBtn = null;
        this.socialLinks = [];
        this.contactLinks = [];
        this.isScrolling = false;
        this.init();
    }

    // ============================================
    // INICIALIZACIÓN DEL COMPONENTE
    // ============================================
    init() {
        this.initializeElements();
        this.syncThemeWithSidebar();
        this.initScrollToTop();
        this.initSocialLinks();
        this.initContactLinks();
        this.initAnimations();
        this.initParticleEffects();
        this.listenForThemeChanges();
        this.startFooterAnimations();
        this.initIntersectionObserver();

        console.log('✅ Tech Footer Component inicializado correctamente');
    }

    // ============================================
    // INICIALIZAR ELEMENTOS DOM
    // ============================================
    initializeElements() {
        this.scrollToTopBtn = document.getElementById('scrollToTop');
        this.socialLinks = document.querySelectorAll('.social-link');
        this.contactLinks = document.querySelectorAll('.info-link');
        this.footerSections = document.querySelectorAll('.footer-section');
        this.featureItems = document.querySelectorAll('.feature-item');
        this.contactItems = document.querySelectorAll('.contact-item');
    }

    // ============================================
    // SINCRONIZACIÓN DE TEMA
    // ============================================
    syncThemeWithSidebar() {
        const savedTheme = localStorage.getItem('techHomeTheme') || 'light';
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
    }

    listenForThemeChanges() {
        // Escuchar cambios de tema desde el sidebar
        document.addEventListener('themeChanged', (e) => {
            this.syncThemeWithSidebar();
        });

        // Monitorear cambios en localStorage
        window.addEventListener('storage', (e) => {
            if (e.key === 'techHomeTheme') {
                this.syncThemeWithSidebar();
            }
        });
    }

    // ============================================
    // FUNCIONALIDAD SCROLL TO TOP
    // ============================================
    initScrollToTop() {
        if (!this.scrollToTopBtn) return;

        // Mostrar/ocultar botón según scroll
        window.addEventListener('scroll', () => {
            this.handleScrollToTopVisibility();
        }, { passive: true });

        // Manejar clic en el botón
        this.scrollToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.smoothScrollToTop();
        });

        // Inicializar visibilidad
        this.handleScrollToTopVisibility();
    }

    handleScrollToTopVisibility() {
        if (this.isScrolling) return;

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const shouldShow = scrollTop > 500;

        if (shouldShow) {
            this.scrollToTopBtn?.classList.add('visible');
        } else {
            this.scrollToTopBtn?.classList.remove('visible');
        }
    }

    smoothScrollToTop() {
        if (this.isScrolling) return;

        this.isScrolling = true;
        
        // Animación suave al top
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        // Animación del botón
        if (this.scrollToTopBtn) {
            this.scrollToTopBtn.style.transform = 'translateY(-10px) scale(1.2) rotate(360deg)';
            
            setTimeout(() => {
                if (this.scrollToTopBtn) {
                    this.scrollToTopBtn.style.transform = '';
                }
                this.isScrolling = false;
            }, 1000);
        }

        // Efecto de partículas
        this.createScrollParticles();
    }

    createScrollParticles() {
        const button = this.scrollToTopBtn;
        if (!button) return;

        const rect = button.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;

        for (let i = 0; i < 8; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: fixed;
                width: 6px;
                height: 6px;
                background: #e94560;
                border-radius: 50%;
                pointer-events: none;
                z-index: 9999;
                left: ${centerX}px;
                top: ${centerY}px;
                animation: scrollParticle 1s ease-out forwards;
            `;

            // Dirección aleatoria
            const angle = (i / 8) * Math.PI * 2;
            particle.style.setProperty('--dx', Math.cos(angle) * 50 + 'px');
            particle.style.setProperty('--dy', Math.sin(angle) * 50 + 'px');

            document.body.appendChild(particle);

            setTimeout(() => {
                if (particle.parentNode) {
                    particle.remove();
                }
            }, 1000);
        }
    }

    // ============================================
    // REDES SOCIALES
    // ============================================
    initSocialLinks() {
        this.socialLinks.forEach(link => {
            // Efectos hover mejorados
            link.addEventListener('mouseenter', () => {
                this.animateSocialLink(link, 'enter');
            });

            link.addEventListener('mouseleave', () => {
                this.animateSocialLink(link, 'leave');
            });

            // Validación y tracking de clics
            link.addEventListener('click', (e) => {
                this.handleSocialClick(e, link);
            });

            // Efectos de ripple
            link.addEventListener('mousedown', (e) => {
                this.createRippleEffect(e, link);
            });
        });
    }

    animateSocialLink(link, action) {
        if (action === 'enter') {
            link.style.transform = 'translateY(-8px) scale(1.1)';
            this.createHoverParticles(link);
        } else {
            link.style.transform = '';
        }
    }

    createHoverParticles(element) {
        const rect = element.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;

        for (let i = 0; i < 4; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: fixed;
                width: 4px;
                height: 4px;
                background: #e94560;
                border-radius: 50%;
                pointer-events: none;
                z-index: 9999;
                left: ${centerX + (Math.random() - 0.5) * 30}px;
                top: ${centerY + (Math.random() - 0.5) * 30}px;
                animation: hoverParticle 0.8s ease-out forwards;
            `;

            document.body.appendChild(particle);

            setTimeout(() => {
                if (particle.parentNode) {
                    particle.remove();
                }
            }, 800);
        }
    }

    createRippleEffect(e, element) {
        const rect = element.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const ripple = document.createElement('div');
        ripple.style.cssText = `
            position: absolute;
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            pointer-events: none;
            left: ${x - 5}px;
            top: ${y - 5}px;
            animation: rippleEffect 0.6s ease-out forwards;
            z-index: 10;
        `;

        element.style.position = 'relative';
        element.appendChild(ripple);

        setTimeout(() => {
            if (ripple.parentNode) {
                ripple.remove();
            }
        }, 600);
    }

    handleSocialClick(e, link) {
        const platform = this.getSocialPlatform(link);
        const url = link.href;

        console.log(`Clic en ${platform}: ${url}`);

        // Validar URL antes de abrir
        if (!this.validateSocialUrl(url)) {
            e.preventDefault();
            this.showNotification(`El enlace de ${platform} no está disponible`, 'warning');
            return;
        }

        // Tracking de analytics (si se implementa)
        this.trackSocialClick(platform, url);
        
        // Animación de confirmación
        this.animateSuccessfulClick(link);
    }

    getSocialPlatform(link) {
        const classList = Array.from(link.classList);
        return classList.find(cls => 
            ['facebook', 'instagram', 'twitter', 'whatsapp', 'linkedin', 'youtube'].includes(cls)
        ) || 'social';
    }

    validateSocialUrl(url) {
        try {
            const urlObj = new URL(url);
            const validDomains = [
                'facebook.com', 'instagram.com', 'twitter.com', 
                'linkedin.com', 'youtube.com', 'wa.me'
            ];
            return validDomains.some(domain => urlObj.hostname.includes(domain));
        } catch {
            return false;
        }
    }

    trackSocialClick(platform, url) {
        // Implementar Google Analytics o tracking personalizado
        if (typeof gtag !== 'undefined') {
            gtag('event', 'social_click', {
                'social_network': platform,
                'social_action': 'click',
                'social_target': url
            });
        }
    }

    animateSuccessfulClick(element) {
        element.style.transform = 'scale(0.95)';
        setTimeout(() => {
            element.style.transform = '';
        }, 150);
    }

    // ============================================
    // ENLACES DE CONTACTO
    // ============================================
    initContactLinks() {
        this.contactLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                this.handleContactClick(e, link);
            });

            // Efectos hover
            link.addEventListener('mouseenter', () => {
                this.animateContactLink(link, true);
            });

            link.addEventListener('mouseleave', () => {
                this.animateContactLink(link, false);
            });
        });
    }

    handleContactClick(e, link) {
        const href = link.getAttribute('href');
        
        if (href.startsWith('tel:')) {
            this.handlePhoneClick(e, link, href);
        } else if (href.startsWith('mailto:')) {
            this.handleEmailClick(e, link, href);
        }
    }

    handlePhoneClick(e, link, href) {
        const phone = href.replace('tel:', '');
        
        // En móviles, permitir llamada directa
        if (this.isMobileDevice()) {
            console.log(`Iniciando llamada a: ${phone}`);
            return; // Permitir comportamiento por defecto
        }
        
        // En desktop, mostrar opciones
        e.preventDefault();
        this.showPhoneOptions(phone);
    }

    handleEmailClick(e, link, href) {
        console.log(`Abriendo cliente de correo para: ${href.replace('mailto:', '')}`);
        // Permitir comportamiento por defecto
    }

    showPhoneOptions(phone) {
        const message = `¿Deseas copiar el número de teléfono?\n\n${phone}`;
        if (confirm(message)) {
            this.copyToClipboard(phone);
            this.showNotification('Número copiado al portapapeles', 'success');
        }
    }

    animateContactLink(link, isHover) {
        if (isHover) {
            link.style.transform = 'translateX(5px)';
        } else {
            link.style.transform = '';
        }
    }

    // ============================================
    // ANIMACIONES Y EFECTOS
    // ============================================
    initAnimations() {
        // Animaciones de entrada para secciones
        this.footerSections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(50px)';
            
            setTimeout(() => {
                section.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, index * 200 + 500);
        });

        // Animación de elementos de características
        this.featureItems.forEach((item, index) => {
            item.addEventListener('mouseenter', () => {
                this.animateFeatureItem(item, true);
            });

            item.addEventListener('mouseleave', () => {
                this.animateFeatureItem(item, false);
            });
        });
    }

    animateFeatureItem(item, isHover) {
        if (isHover) {
            item.style.transform = 'translateX(8px) scale(1.02)';
            this.createFeatureGlow(item);
        } else {
            item.style.transform = '';
        }
    }

    createFeatureGlow(element) {
        const glow = document.createElement('div');
        glow.style.cssText = `
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(90deg, transparent, rgba(233, 69, 96, 0.3), transparent);
            border-radius: 14px;
            pointer-events: none;
            z-index: -1;
            animation: glowMove 2s ease-in-out infinite;
        `;

        element.style.position = 'relative';
        element.appendChild(glow);

        setTimeout(() => {
            if (glow.parentNode) {
                glow.remove();
            }
        }, 2000);
    }

    initParticleEffects() {
        // Crear partículas de fondo periódicamente
        setInterval(() => {
            this.createBackgroundParticle();
        }, 2000);
    }

    createBackgroundParticle() {
        const footer = document.querySelector('.tech-footer');
        if (!footer) return;

        const particle = document.createElement('div');
        particle.style.cssText = `
            position: absolute;
            width: ${Math.random() * 6 + 2}px;
            height: ${Math.random() * 6 + 2}px;
            background: rgba(233, 69, 96, ${Math.random() * 0.3 + 0.1});
            border-radius: 50%;
            pointer-events: none;
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100}%;
            animation: floatParticle ${Math.random() * 4 + 3}s ease-out forwards;
            z-index: 1;
        `;

        const particleContainer = footer.querySelector('.footer-particles');
        if (particleContainer) {
            particleContainer.appendChild(particle);
            
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.remove();
                }
            }, 7000);
        }
    }

    startFooterAnimations() {
        // Animación del gradiente de fondo
        setInterval(() => {
            const gradient = document.querySelector('.footer-gradient');
            if (gradient) {
                gradient.style.opacity = Math.random() * 0.3 + 0.7;
            }
        }, 3000);
    }

    // ============================================
    // INTERSECTION OBSERVER PARA ANIMACIONES
    // ============================================
    initIntersectionObserver() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('footer-visible');
                    this.animateOnScroll(entry.target);
                }
            });
        }, observerOptions);

        // Observar elementos animables
        this.contactItems.forEach(item => observer.observe(item));
        this.featureItems.forEach(item => observer.observe(item));
    }

    animateOnScroll(element) {
        element.style.transform = 'translateX(0) scale(1)';
        element.style.opacity = '1';
    }

    // ============================================
    // UTILIDADES
    // ============================================
    isMobileDevice() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    copyToClipboard(text) {
        if (navigator.clipboard) {
            return navigator.clipboard.writeText(text);
        } else {
            // Fallback para navegadores más antiguos
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        }
    }

    showNotification(message, type = 'info') {
        // Crear notificación temporal
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10B981' : type === 'warning' ? '#F59E0B' : '#3B82F6'};
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            font-weight: 500;
            z-index: 10000;
            animation: notificationSlide 0.4s ease-out;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        `;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'notificationSlideOut 0.4s ease-in forwards';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 400);
        }, 3000);
    }

    // ============================================
    // MÉTODOS PÚBLICOS
    // ============================================
    updateContactInfo(contactData) {
        if (contactData.phone) {
            const phoneLink = document.querySelector('a[href^="tel:"]');
            if (phoneLink) {
                phoneLink.href = `tel:${contactData.phone}`;
                phoneLink.textContent = contactData.phone;
            }
        }

        if (contactData.email) {
            const emailLink = document.querySelector('a[href^="mailto:"]');
            if (emailLink) {
                emailLink.href = `mailto:${contactData.email}`;
                emailLink.textContent = contactData.email;
            }
        }
    }

    updateSocialLinks(socialData) {
        Object.keys(socialData).forEach(platform => {
            const link = document.querySelector(`.social-link.${platform}`);
            if (link && socialData[platform]) {
                link.href = socialData[platform];
            }
        });
    }

    scrollToFooter() {
        const footer = document.querySelector('.tech-footer');
        if (footer) {
            footer.scrollIntoView({ behavior: 'smooth' });
        }
    }
}

// ============================================
// ESTILOS CSS PARA ANIMACIONES
// ============================================
if (!document.getElementById('footer-animations')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'footer-animations';
    styleSheet.textContent = `
        @keyframes scrollParticle {
            0% { 
                transform: translate(0, 0) scale(1);
                opacity: 1; 
            }
            100% { 
                transform: translate(var(--dx), var(--dy)) scale(0);
                opacity: 0; 
            }
        }

        @keyframes hoverParticle {
            0% { 
                transform: scale(0);
                opacity: 0.8; 
            }
            50% { 
                transform: scale(1);
                opacity: 1; 
            }
            100% { 
                transform: scale(0) translateY(-20px);
                opacity: 0; 
            }
        }

        @keyframes rippleEffect {
            0% { 
                transform: scale(0);
                opacity: 0.6; 
            }
            100% { 
                transform: scale(10);
                opacity: 0; 
            }
        }

        @keyframes glowMove {
            0% { 
                transform: translateX(-100%); 
            }
            100% { 
                transform: translateX(100%); 
            }
        }

        @keyframes floatParticle {
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
                transform: translateY(-50px) rotate(360deg);
                opacity: 0; 
            }
        }

        @keyframes notificationSlide {
            0% { 
                transform: translateX(100%);
                opacity: 0; 
            }
            100% { 
                transform: translateX(0);
                opacity: 1; 
            }
        }

        @keyframes notificationSlideOut {
            0% { 
                transform: translateX(0);
                opacity: 1; 
            }
            100% { 
                transform: translateX(100%);
                opacity: 0; 
            }
        }
    `;
    document.head.appendChild(styleSheet);
}

// ============================================
// INICIALIZACIÓN AUTOMÁTICA
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Crear instancia del componente
    window.techFooter = new TechFooterComponent();
});

// ============================================
// API PÚBLICA SIMPLIFICADA
// ============================================
window.TechFooter = {
    updateContact: (contactData) => window.techFooter?.updateContactInfo(contactData),
    updateSocial: (socialData) => window.techFooter?.updateSocialLinks(socialData),
    scrollToFooter: () => window.techFooter?.scrollToFooter(),
    scrollToTop: () => window.techFooter?.smoothScrollToTop()
};