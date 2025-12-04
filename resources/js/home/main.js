/**
 * ============================================
 * TECH HOME - JavaScript Principal para Welcome
 * Funcionalidades específicas para welcome.blade.php
 * ============================================
 */

// Namespace para TECH HOME
window.TechHome = window.TechHome || {};

// Configuración global
TechHome.config = {
    animationSpeed: 300,
    clockUpdateInterval: 1000,
    notificationDuration: 5000,
    autoSaveInterval: 30000
};

// Sistema de Inicialización
TechHome.init = function() {
    console.log('🚀 Inicializando TECH HOME Sistema...');
    
    // Verificar dependencias
    if (typeof document === 'undefined') {
        console.error('❌ DOM no disponible');
        return;
    }

    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', this.startSystem.bind(this));
    } else {
        this.startSystem();
    }
};

// Iniciar todos los sistemas
TechHome.startSystem = function() {
    try {
        this.initClock();
        this.initTabs();
        this.initNotifications();
        this.initInteractions();
        this.initStats();
        this.initAccessibility();
        
        console.log('✅ TECH HOME Sistema iniciado correctamente');
        
        // Mostrar notificación de bienvenida
        setTimeout(() => {
            this.showNotification('¡Bienvenido a TECH HOME!', 'success');
        }, 1000);
        
    } catch (error) {
        console.error('❌ Error iniciando sistema:', error);
        this.showNotification('Error al iniciar el sistema', 'error');
    }
};

// Sistema de Reloj Digital
TechHome.initClock = function() {
    const clockElement = document.querySelector('.digital-clock');
    if (!clockElement) return;

    const updateClock = () => {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        
        const dateString = now.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        clockElement.innerHTML = `
            <div class="time">${timeString}</div>
            <div class="date" style="font-size: 0.8em; opacity: 0.8;">${dateString}</div>
        `;
    };

    // Actualizar inmediatamente y luego cada segundo
    updateClock();
    setInterval(updateClock, this.config.clockUpdateInterval);
    
    console.log('⏰ Sistema de reloj iniciado');
};

// Sistema de Pestañas
TechHome.initTabs = function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    if (tabButtons.length === 0) return;

    tabButtons.forEach((button, index) => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Remover clases activas
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.style.display = 'none';
            });
            
            // Activar pestaña seleccionada
            button.classList.add('active');
            
            // Mostrar contenido correspondiente
            const targetTab = button.getAttribute('data-tab') || `tab-${index + 1}`;
            const targetContent = document.querySelector(`[data-tab-content="${targetTab}"]`) 
                                || document.querySelectorAll('.tab-content')[index];
            
            if (targetContent) {
                targetContent.classList.add('active');
                targetContent.style.display = 'block';
                
                // Efecto de entrada
                targetContent.style.opacity = '0';
                targetContent.style.transform = 'translateY(10px)';
                
                setTimeout(() => {
                    targetContent.style.transition = 'all 0.3s ease';
                    targetContent.style.opacity = '1';
                    targetContent.style.transform = 'translateY(0)';
                }, 50);
            }
            
            // Actualizar estadísticas si es necesario
            this.updateTabStats(targetTab);
        });
    });
    
    // Activar primera pestaña por defecto
    if (tabButtons[0]) {
        tabButtons[0].click();
    }
    
    console.log('📑 Sistema de pestañas iniciado');
};

// Sistema de Notificaciones
TechHome.initNotifications = function() {
    // Crear contenedor si no existe
    if (!document.getElementById('tech-notifications')) {
        const container = document.createElement('div');
        container.id = 'tech-notifications';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        `;
        document.body.appendChild(container);
    }
    
    console.log('🔔 Sistema de notificaciones iniciado');
};

// Mostrar Notificación
TechHome.showNotification = function(message, type = 'info', duration = null) {
    const container = document.getElementById('tech-notifications');
    if (!container) return;
    
    const notification = document.createElement('div');
    const notificationId = 'notification-' + Date.now();
    
    notification.id = notificationId;
    notification.style.cssText = `
        background: ${this.getNotificationColor(type)};
        color: white;
        padding: 15px 20px;
        margin-bottom: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    `;
    
    notification.innerHTML = `
        <i class="fas ${this.getNotificationIcon(type)}"></i>
        <span>${message}</span>
        <button onclick="TechHome.closeNotification('${notificationId}')" 
                style="background:none;border:none;color:white;font-size:18px;cursor:pointer;margin-left:auto;">
            ×
        </button>
    `;
    
    container.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-cerrar
    const autoCloseDuration = duration || this.config.notificationDuration;
    setTimeout(() => {
        this.closeNotification(notificationId);
    }, autoCloseDuration);
};

// Cerrar Notificación
TechHome.closeNotification = function(notificationId) {
    const notification = document.getElementById(notificationId);
    if (notification) {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
};

// Obtener Color de Notificación
TechHome.getNotificationColor = function(type) {
    const colors = {
        success: '#10b981',
        error: '#dc2626',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    return colors[type] || colors.info;
};

// Obtener Ícono de Notificación
TechHome.getNotificationIcon = function(type) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    return icons[type] || icons.info;
};

// Sistema de Interacciones
TechHome.initInteractions = function() {
    // Botones de acción rápida
    this.initQuickActionButtons();
    
    // Botones de biblioteca
    this.initLibraryButtons();
    
    // Botones de componentes
    this.initComponentButtons();
    
    // Efectos hover mejorados
    this.initHoverEffects();
    
    console.log('🎯 Sistema de interacciones iniciado');
};

// Botones de Acción Rápida
TechHome.initQuickActionButtons = function() {
    const quickButtons = document.querySelectorAll('.quick-action-button');
    
    quickButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            
            const card = button.closest('.quick-action-card');
            const title = card.querySelector('.quick-action-title')?.textContent || 'Acción';
            
            // Efecto visual
            button.style.transform = 'scale(0.95)';
            setTimeout(() => {
                button.style.transform = '';
            }, 150);
            
            // Simular navegación
            this.simulateNavigation(title);
        });
    });
};

// Botones de Biblioteca
TechHome.initLibraryButtons = function() {
    const libraryButtons = document.querySelectorAll('.library-button');
    
    libraryButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            
            const card = button.closest('.library-card');
            const title = card.querySelector('.library-title')?.textContent || 'Biblioteca';
            const action = button.textContent.trim();
            
            // Efecto loading
            const originalText = button.textContent;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
            button.disabled = true;
            
            setTimeout(() => {
                button.textContent = originalText;
                button.disabled = false;
                this.showNotification(`${action} - ${title}`, 'success');
            }, 1500);
        });
    });
};

// Botones de Componentes
TechHome.initComponentButtons = function() {
    const componentButtons = document.querySelectorAll('.component-button');
    
    componentButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            
            const card = button.closest('.component-card');
            const title = card.querySelector('.component-title')?.textContent || 'Componente';
            
            this.showNotification(`Explorando ${title}`, 'info');
            this.simulateNavigation(title);
        });
    });
};

// Efectos Hover Mejorados
TechHome.initHoverEffects = function() {
    const cards = document.querySelectorAll('.quick-action-card, .library-card, .component-card, .status-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
};

// Sistema de Estadísticas
TechHome.initStats = function() {
    this.animateNumbers();
    this.updateSystemStatus();
    
    // Actualizar estadísticas periódicamente
    setInterval(() => {
        this.updateSystemStatus();
    }, 30000);
    
    console.log('📊 Sistema de estadísticas iniciado');
};

// Animar Números
TechHome.animateNumbers = function() {
    const numbers = document.querySelectorAll('.stat-number, .status-number');
    
    numbers.forEach(numberElement => {
        const finalNumber = parseInt(numberElement.textContent) || 0;
        let currentNumber = 0;
        const increment = Math.ceil(finalNumber / 30);
        
        const animation = setInterval(() => {
            currentNumber += increment;
            if (currentNumber >= finalNumber) {
                currentNumber = finalNumber;
                clearInterval(animation);
            }
            numberElement.textContent = currentNumber;
        }, 50);
    });
};

// Actualizar Estado del Sistema
TechHome.updateSystemStatus = function() {
    const statusCards = document.querySelectorAll('.status-card');
    
    statusCards.forEach(card => {
        const indicator = card.querySelector('.status-indicator');
        if (indicator) {
            // Simular actualización de estado
            const statuses = ['active', 'available', 'progress'];
            const randomStatus = statuses[Math.floor(Math.random() * statuses.length)];
            
            indicator.className = `status-indicator ${randomStatus}`;
        }
    });
};

// Actualizar Estadísticas de Pestañas
TechHome.updateTabStats = function(tabId) {
    // Simular carga de estadísticas específicas por pestaña
    const tabContent = document.querySelector(`[data-tab-content="${tabId}"]`);
    if (tabContent) {
        const numbers = tabContent.querySelectorAll('.stat-number, .status-number');
        
        // Re-animar números al cambiar de pestaña
        setTimeout(() => {
            this.animateNumbers();
        }, 300);
    }
};

// Simular Navegación
TechHome.simulateNavigation = function(section) {
    this.showNotification(`Navegando a ${section}...`, 'info', 2000);
    
    // Simular tiempo de carga
    setTimeout(() => {
        this.showNotification(`Sección ${section} cargada`, 'success', 3000);
    }, 2000);
};

// Sistema de Accesibilidad
TechHome.initAccessibility = function() {
    // Navegación por teclado
    document.addEventListener('keydown', (e) => {
        // Escape cierra notificaciones
        if (e.key === 'Escape') {
            const notifications = document.querySelectorAll('[id^="notification-"]');
            notifications.forEach(notification => {
                this.closeNotification(notification.id);
            });
        }
        
        // Tab navigation mejorada
        if (e.key === 'Tab') {
            const focusableElements = document.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            
            focusableElements.forEach(el => {
                el.addEventListener('focus', function() {
                    this.style.outline = '2px solid #dc2626';
                    this.style.outlineOffset = '2px';
                });
                
                el.addEventListener('blur', function() {
                    this.style.outline = '';
                    this.style.outlineOffset = '';
                });
            });
        }
    });
    
    console.log('♿ Sistema de accesibilidad iniciado');
};

// Utilidades
TechHome.utils = {
    // Formatear números
    formatNumber: function(num) {
        return new Intl.NumberFormat('es-ES').format(num);
    },
    
    // Debounce function
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Throttle function
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }
};

// Inicializar cuando se carga el script
TechHome.init();