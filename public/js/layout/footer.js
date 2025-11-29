/**
 * TECH HOME - FOOTER FUNCTIONALITY
 * ===================================
 * JavaScript para la funcionalidad del footer
 * Incluye: newsletter, animaciones, efectos interactivos
 */

class TechFooter {
    constructor() {
        this.newsletterForm = null;
        this.socialLinks = [];
        this.contactAnimations = [];
        this.currentYear = new Date().getFullYear();
        
        this.init();
    }

    /**
     * Inicialización del footer
     */
    init() {
        this.setupNewsletterForm();
        this.setupSocialLinks();
        this.setupContactAnimations();
        this.setupScrollAnimations();
        this.updateCopyright();
        this.setupEmailProtection();
        
        // Debug
        console.log('🎯 TECH HOME Footer: Initialized successfully');
    }

    /**
     * Configurar formulario de newsletter
     */
    setupNewsletterForm() {
        this.newsletterForm = document.querySelector('.newsletter-form');
        
        if (!this.newsletterForm) return;

        const form = this.newsletterForm.querySelector('form') || this.createNewsletterForm();
        const input = form.querySelector('input[type="email"]');
        const button = form.querySelector('.newsletter-btn');

        if (input && button) {
            // Validación en tiempo real
            input.addEventListener('input', (e) => {
                this.validateEmail(e.target);
            });

            // Envío del formulario
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleNewsletterSubmit(input.value);
            });

            // Efectos visuales del botón
            button.addEventListener('mouseenter', () => {
                this.animateButton(button);
            });
        }
    }

    /**
     * Crear formulario de newsletter si no existe
     */
    createNewsletterForm() {
        const formHTML = `
            <form class="newsletter-form-inner">
                <div class="newsletter-input-group">
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Tu correo electrónico"
                        required
                        autocomplete="email"
                    >
                    <div class="input-validation"></div>
                </div>
                <button type="submit" class="newsletter-btn">
                    <i class="fas fa-paper-plane"></i>
                    <span>Suscribirse</span>
                </button>
            </form>
        `;

        this.newsletterForm.insertAdjacentHTML('beforeend', formHTML);
        return this.newsletterForm.querySelector('form');
    }

    /**
     * Validar email en tiempo real
     */
    validateEmail(input) {
        const email = input.value.trim();
        const validation = input.parentElement.querySelector('.input-validation');
        
        if (!validation) return;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email === '') {
            validation.textContent = '';
            input.style.borderColor = 'var(--border-color)';
        } else if (!emailRegex.test(email)) {
            validation.textContent = '❌ Email inválido';
            validation.style.color = '#ef4444';
            input.style.borderColor = '#ef4444';
        } else {
            validation.textContent = '✅ Email válido';
            validation.style.color = '#22c55e';
            input.style.borderColor = '#22c55e';
        }
    }

    /**
     * Manejar envío de newsletter
     */
    async handleNewsletterSubmit(email) {
        const button = this.newsletterForm.querySelector('.newsletter-btn');
        const originalContent = button.innerHTML;

        try {
            // Mostrar estado de carga
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Enviando...</span>';
            button.disabled = true;

            // Simular llamada API (reemplazar con llamada real)
            const success = await this.subscribeToNewsletter(email);

            if (success) {
                this.showNewsletterSuccess();
                this.resetNewsletterForm();
            } else {
                throw new Error('Error en la suscripción');
            }

        } catch (error) {
            this.showNewsletterError();
        } finally {
            // Restaurar botón
            setTimeout(() => {
                button.innerHTML = originalContent;
                button.disabled = false;
            }, 2000);
        }
    }

    /**
     * Suscribir al newsletter (simulado)
     */
    async subscribeToNewsletter(email) {
        // Simular llamada a API
        return new Promise((resolve) => {
            setTimeout(() => {
                // Simular éxito con 90% de probabilidad
                resolve(Math.random() > 0.1);
            }, 2000);
        });
    }

    /**
     * Mostrar mensaje de éxito del newsletter
     */
    showNewsletterSuccess() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¡Suscripción exitosa!',
                text: 'Te has suscrito correctamente al newsletter. ¡Gracias por unirte!',
                icon: 'success',
                confirmButtonColor: '#dc2626',
                background: document.body.classList.contains('ithr-dark-mode') ? '#1f2937' : '#ffffff'
            });
        } else {
            this.showToast('✅ ¡Te has suscrito exitosamente al newsletter!', 'success');
        }
    }

    /**
     * Mostrar mensaje de error del newsletter
     */
    showNewsletterError() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error en la suscripción',
                text: 'Hubo un problema al procesar tu suscripción. Por favor, inténtalo de nuevo.',
                icon: 'error',
                confirmButtonColor: '#dc2626',
                background: document.body.classList.contains('ithr-dark-mode') ? '#1f2937' : '#ffffff'
            });
        } else {
            this.showToast('❌ Error en la suscripción. Inténtalo de nuevo.', 'error');
        }
    }

    /**
     * Resetear formulario de newsletter
     */
    resetNewsletterForm() {
        const form = this.newsletterForm.querySelector('form');
        if (form) {
            form.reset();
            
            const validation = form.querySelector('.input-validation');
            if (validation) {
                validation.textContent = '';
            }

            const input = form.querySelector('input[type="email"]');
            if (input) {
                input.style.borderColor = 'var(--border-color)';
            }
        }
    }

    /**
     * Configurar enlaces de redes sociales
     */
    setupSocialLinks() {
        this.socialLinks = document.querySelectorAll('.social-link');
        
        this.socialLinks.forEach((link, index) => {
            // Animación de entrada escalonada
            setTimeout(() => {
                link.style.opacity = '1';
                link.style.transform = 'translateY(0)';
            }, index * 100);

            // Efectos de hover
            link.addEventListener('mouseenter', () => {
                this.animateSocialIcon(link);
            });

            // Tracking de clicks (para analytics)
            link.addEventListener('click', () => {
                this.trackSocialClick(link);
            });
        });
    }

    /**
     * Animar icono de red social
     */
    animateSocialIcon(link) {
        const icon = link.querySelector('i');
        if (icon) {
            icon.style.animation = 'none';
            requestAnimationFrame(() => {
                icon.style.animation = 'socialBounce 0.4s ease-out';
            });
        }
    }

    /**
     * Rastrear click en redes sociales
     */
    trackSocialClick(link) {
        const platform = this.getSocialPlatform(link);
        
        // Aquí se podría integrar con Google Analytics o similar
        console.log(`📊 Social click tracked: ${platform}`);
        
        // Mostrar feedback visual
        this.showToast(`Abriendo ${platform}...`, 'info');
    }

    /**
     * Obtener plataforma de red social
     */
    getSocialPlatform(link) {
        const href = link.getAttribute('href') || '';
        const icon = link.querySelector('i').className;
        
        if (href.includes('facebook') || icon.includes('facebook')) return 'Facebook';
        if (href.includes('twitter') || icon.includes('twitter')) return 'Twitter';
        if (href.includes('instagram') || icon.includes('instagram')) return 'Instagram';
        if (href.includes('linkedin') || icon.includes('linkedin')) return 'LinkedIn';
        if (href.includes('youtube') || icon.includes('youtube')) return 'YouTube';
        if (href.includes('github') || icon.includes('github')) return 'GitHub';
        
        return 'Red Social';
    }

    /**
     * Configurar animaciones de contacto
     */
    setupContactAnimations() {
        const contactItems = document.querySelectorAll('.contact-info li');
        
        contactItems.forEach((item, index) => {
            // Animación de entrada escalonada
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, index * 150);

            // Efecto de hover mejorado
            item.addEventListener('mouseenter', () => {
                this.animateContactItem(item);
            });

            // Click en elementos de contacto
            item.addEventListener('click', () => {
                this.handleContactClick(item);
            });
        });
    }

    /**
     * Animar elemento de contacto
     */
    animateContactItem(item) {
        const icon = item.querySelector('.contact-icon');
        if (icon) {
            icon.style.animation = 'none';
            requestAnimationFrame(() => {
                icon.style.animation = 'contactPulse 0.6s ease-out';
            });
        }
    }

    /**
     * Manejar click en información de contacto
     */
    handleContactClick(item) {
        const text = item.textContent.trim();
        
        if (text.includes('@')) {
            // Es un email
            const email = this.extractEmail(text);
            if (email) {
                this.copyToClipboard(email);
                this.showToast(`📧 Email copiado: ${email}`, 'success');
            }
        } else if (text.match(/[\d\s\-\+\(\)]+/)) {
            // Es un teléfono
            const phone = this.extractPhone(text);
            if (phone) {
                this.copyToClipboard(phone);
                this.showToast(`📞 Teléfono copiado: ${phone}`, 'success');
            }
        } else {
            // Es una dirección u otra información
            this.copyToClipboard(text);
            this.showToast('📋 Información copiada al portapapeles', 'success');
        }
    }

    /**
     * Extraer email del texto
     */
    extractEmail(text) {
        const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/;
        const match = text.match(emailRegex);
        return match ? match[0] : null;
    }

    /**
     * Extraer teléfono del texto
     */
    extractPhone(text) {
        const phoneRegex = /[\+]?[\d\s\-\(\)]+/;
        const match = text.match(phoneRegex);
        return match ? match[0].trim() : null;
    }

    /**
     * Copiar al portapapeles
     */
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
        } catch (err) {
            // Fallback para navegadores antiguos
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }
    }

    /**
     * Configurar animaciones de scroll
     */
    setupScrollAnimations() {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        
                        // Animación especial para el footer
                        if (entry.target.classList.contains('tech-footer')) {
                            this.startFooterAnimations();
                        }
                    }
                });
            },
            { 
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        const footer = document.querySelector('.tech-footer');
        if (footer) {
            observer.observe(footer);
        }

        const footerSections = document.querySelectorAll('.footer-section');
        footerSections.forEach(section => {
            observer.observe(section);
        });
    }

    /**
     * Iniciar animaciones del footer
     */
    startFooterAnimations() {
        // Animar elementos del footer con delay escalonado
        const elements = [
            '.footer-brand',
            '.footer-section:nth-child(2)',
            '.footer-section:nth-child(3)',
            '.footer-section:nth-child(4)'
        ];

        elements.forEach((selector, index) => {
            const element = document.querySelector(selector);
            if (element) {
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 200);
            }
        });
    }

    /**
     * Actualizar año de copyright
     */
    updateCopyright() {
        const copyrightElement = document.querySelector('.footer-copyright');
        if (copyrightElement) {
            let text = copyrightElement.textContent;
            // Reemplazar cualquier año de 4 dígitos con el año actual
            text = text.replace(/\d{4}/, this.currentYear);
            copyrightElement.textContent = text;
        }
    }

    /**
     * Proteger emails del spam
     */
    setupEmailProtection() {
        const emailElements = document.querySelectorAll('[data-email]');
        
        emailElements.forEach(element => {
            const encryptedEmail = element.getAttribute('data-email');
            if (encryptedEmail) {
                // Decodificar email (implementar según necesidades)
                const decodedEmail = this.decodeEmail(encryptedEmail);
                element.textContent = decodedEmail;
                
                // Convertir a enlace mailto al hacer click
                element.addEventListener('click', () => {
                    window.location.href = `mailto:${decodedEmail}`;
                });
            }
        });
    }

    /**
     * Decodificar email (ejemplo simple)
     */
    decodeEmail(encoded) {
        // Implementar decodificación según el método usado
        // Por ejemplo, ROT13 o base64
        return atob(encoded); // Ejemplo con base64
    }

    /**
     * Animar botón
     */
    animateButton(button) {
        button.style.animation = 'none';
        requestAnimationFrame(() => {
            button.style.animation = 'buttonPulse 0.4s ease-out';
        });
    }

    /**
     * Mostrar toast notification
     */
    showToast(message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: document.body.classList.contains('ithr-dark-mode') ? '#374151' : '#ffffff'
            });
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }

    /**
     * Obtener estadísticas del footer
     */
    getFooterStats() {
        return {
            socialLinks: this.socialLinks.length,
            newsletterSubscribed: this.newsletterForm ? true : false,
            currentYear: this.currentYear,
            contactItems: document.querySelectorAll('.contact-info li').length
        };
    }

    /**
     * Cleanup al destruir
     */
    destroy() {
        // Remover event listeners y limpiar intervalos
        this.socialLinks.forEach(link => {
            link.removeEventListener('mouseenter', this.animateSocialIcon);
            link.removeEventListener('click', this.trackSocialClick);
        });

        console.log('🎯 TECH HOME Footer: Destroyed');
    }
}

// Estilos CSS adicionales para animaciones
const footerAnimationStyles = `
    <style id="footer-animation-styles">
        /* Animaciones para redes sociales */
        @keyframes socialBounce {
            0%, 100% { transform: scale(1) rotate(0deg); }
            25% { transform: scale(1.1) rotate(-5deg); }
            75% { transform: scale(1.1) rotate(5deg); }
        }

        /* Animaciones para contacto */
        @keyframes contactPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4); }
        }

        /* Animaciones para botones */
        @keyframes buttonPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        /* Validación de inputs */
        .input-validation {
            font-size: 0.75rem;
            margin-top: 0.3rem;
            min-height: 1rem;
            transition: all 0.3s ease;
        }

        /* Estados de animación */
        .footer-section,
        .footer-brand {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .footer-section.animate-in,
        .footer-brand.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        .social-link {
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.4s ease;
        }

        .contact-info li {
            opacity: 0;
            transform: translateX(-20px);
            transition: all 0.5s ease;
        }

        /* Efectos de hover mejorados */
        .newsletter-btn {
            position: relative;
            overflow: hidden;
        }

        .newsletter-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .newsletter-btn:disabled:hover {
            transform: none;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .footer-section,
            .footer-brand {
                opacity: 1;
                transform: translateY(0);
            }

            .social-link,
            .contact-info li {
                opacity: 1;
                transform: translateY(0) translateX(0);
            }
        }
    </style>
`;

// Inyectar estilos si no existen
if (!document.querySelector('#footer-animation-styles')) {
    document.head.insertAdjacentHTML('beforeend', footerAnimationStyles);
}

// Inicialización automática cuando el DOM esté listo
let techFooter;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        techFooter = new TechFooter();
    });
} else {
    techFooter = new TechFooter();
}

// Exposición global para uso externo
window.TechFooter = TechFooter;
window.techFooter = techFooter;

// Cleanup al cerrar la página
window.addEventListener('beforeunload', () => {
    if (techFooter) {
        techFooter.destroy();
    }
});