/**
 * TECH HOME - HEADER FUNCTIONALITY
 * ===================================
 * JavaScript para la funcionalidad del header principal
 * Incluye: fecha/hora, notificaciones, avatar interactivo
 */

class TechHeader {
    constructor() {
        this.currentTime = null;
        this.currentDate = null;
        this.notifications = [];
        this.unreadCount = 0;
        this.refreshInterval = null;
        
        this.init();
    }

    /**
     * Inicialización del header
     */
    init() {
        this.setupDateTime();
        this.setupNotifications();
        this.setupUserInteractions();
        this.setupScrollEffects();
        this.startRealTimeUpdates();
        
        // Debug
        console.log('🎯 TECH HOME Header: Initialized successfully');
    }

    /**
     * Configuración de fecha y hora
     */
    setupDateTime() {
        this.updateDateTime();
        
        // Actualizar cada segundo
        this.refreshInterval = setInterval(() => {
            this.updateDateTime();
        }, 1000);
    }

    /**
     * Actualizar fecha y hora en tiempo real
     */
    updateDateTime() {
        const now = new Date();
        
        // Configuración de formato en español
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        
        const dateOptions = {
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        };

        // Actualizar elementos si existen
        const timeElement = document.querySelector('.datetime-item span[data-time]');
        const dateElement = document.querySelector('.datetime-item span[data-date]');
        
        if (timeElement) {
            timeElement.textContent = now.toLocaleTimeString('es-ES', timeOptions);
        }
        
        if (dateElement) {
            dateElement.textContent = now.toLocaleDateString('es-ES', dateOptions);
        }

        // Guardar valores actuales
        this.currentTime = now.toLocaleTimeString('es-ES', timeOptions);
        this.currentDate = now.toLocaleDateString('es-ES', dateOptions);
    }

    /**
     * Configuración del sistema de notificaciones
     */
    setupNotifications() {
        const notificationBtn = document.querySelector('.notifications-btn');
        
        if (notificationBtn) {
            notificationBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleNotificationPanel();
            });

            // Simular notificaciones para demo
            this.simulateNotifications();
        }
    }

    /**
     * Alternar panel de notificaciones
     */
    toggleNotificationPanel() {
        // Verificar si ya existe el panel
        let panel = document.querySelector('.notifications-panel');
        
        if (panel) {
            this.hideNotificationPanel();
        } else {
            this.showNotificationPanel();
        }
    }

    /**
     * Mostrar panel de notificaciones
     */
    showNotificationPanel() {
        const header = document.querySelector('.tech-header');
        if (!header) return;

        const panel = document.createElement('div');
        panel.className = 'notifications-panel';
        panel.innerHTML = this.generateNotificationPanelHTML();
        
        header.appendChild(panel);
        
        // Animación de entrada
        requestAnimationFrame(() => {
            panel.classList.add('show');
        });

        // Cerrar al hacer click fuera
        setTimeout(() => {
            document.addEventListener('click', this.handleOutsideClick.bind(this));
        }, 100);
    }

    /**
     * Ocultar panel de notificaciones
     */
    hideNotificationPanel() {
        const panel = document.querySelector('.notifications-panel');
        if (panel) {
            panel.classList.remove('show');
            setTimeout(() => {
                panel.remove();
                document.removeEventListener('click', this.handleOutsideClick);
            }, 300);
        }
    }

    /**
     * Manejar clicks fuera del panel
     */
    handleOutsideClick(e) {
        const panel = document.querySelector('.notifications-panel');
        const notificationBtn = document.querySelector('.notifications-btn');
        
        if (panel && !panel.contains(e.target) && !notificationBtn.contains(e.target)) {
            this.hideNotificationPanel();
        }
    }

    /**
     * Generar HTML del panel de notificaciones
     */
    generateNotificationPanelHTML() {
        return `
            <div class="notification-header">
                <h4><i class="fas fa-bell"></i> Notificaciones</h4>
                <button class="mark-all-read" onclick="techHeader.markAllAsRead()">
                    <i class="fas fa-check-double"></i> Marcar todas
                </button>
            </div>
            <div class="notification-list">
                ${this.notifications.length > 0 ? 
                    this.notifications.map(notification => this.generateNotificationHTML(notification)).join('') :
                    '<div class="no-notifications"><i class="fas fa-inbox"></i><p>No tienes notificaciones</p></div>'
                }
            </div>
            <div class="notification-footer">
                <a href="/notifications" class="view-all-btn">
                    <i class="fas fa-eye"></i> Ver todas las notificaciones
                </a>
            </div>
        `;
    }

    /**
     * Generar HTML de una notificación individual
     */
    generateNotificationHTML(notification) {
        const timeAgo = this.getTimeAgo(notification.timestamp);
        const unreadClass = !notification.read ? 'unread' : '';
        
        return `
            <div class="notification-item ${unreadClass}" data-id="${notification.id}">
                <div class="notification-icon ${notification.type}">
                    <i class="${notification.icon}"></i>
                </div>
                <div class="notification-content">
                    <h5>${notification.title}</h5>
                    <p>${notification.message}</p>
                    <span class="notification-time">
                        <i class="far fa-clock"></i> ${timeAgo}
                    </span>
                </div>
                <button class="notification-close" onclick="techHeader.dismissNotification('${notification.id}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }

    /**
     * Simular notificaciones para demostración
     */
    simulateNotifications() {
        this.notifications = [
            {
                id: 'notif_1',
                type: 'info',
                icon: 'fas fa-graduation-cap',
                title: 'Nuevo curso disponible',
                message: 'Se ha publicado el curso "Laravel Avanzado"',
                timestamp: Date.now() - (5 * 60 * 1000), // 5 minutos atrás
                read: false
            },
            {
                id: 'notif_2',
                type: 'success',
                icon: 'fas fa-trophy',
                title: '¡Certificado obtenido!',
                message: 'Has completado exitosamente el curso de JavaScript',
                timestamp: Date.now() - (30 * 60 * 1000), // 30 minutos atrás
                read: false
            },
            {
                id: 'notif_3',
                type: 'warning',
                icon: 'fas fa-clock',
                title: 'Recordatorio',
                message: 'Tienes una clase programada en 1 hora',
                timestamp: Date.now() - (2 * 60 * 60 * 1000), // 2 horas atrás
                read: true
            }
        ];

        this.updateNotificationBadge();
    }

    /**
     * Actualizar badge de notificaciones
     */
    updateNotificationBadge() {
        this.unreadCount = this.notifications.filter(n => !n.read).length;
        
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            if (this.unreadCount > 0) {
                badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    markAllAsRead() {
        this.notifications.forEach(notification => {
            notification.read = true;
        });
        
        this.updateNotificationBadge();
        
        // Actualizar panel si está abierto
        const panel = document.querySelector('.notifications-panel');
        if (panel) {
            panel.innerHTML = this.generateNotificationPanelHTML();
        }

        // Mostrar mensaje de éxito
        this.showToast('Todas las notificaciones han sido marcadas como leídas', 'success');
    }

    /**
     * Descartar notificación específica
     */
    dismissNotification(notificationId) {
        this.notifications = this.notifications.filter(n => n.id !== notificationId);
        this.updateNotificationBadge();
        
        // Actualizar panel
        const panel = document.querySelector('.notifications-panel');
        if (panel) {
            panel.innerHTML = this.generateNotificationPanelHTML();
        }

        this.showToast('Notificación eliminada', 'info');
    }

    /**
     * Configurar interacciones de usuario
     */
    setupUserInteractions() {
        const userAvatar = document.querySelector('.user-avatar');
        const logoutBtn = document.querySelector('.logout-btn');
        
        // Efecto hover en avatar
        if (userAvatar) {
            userAvatar.addEventListener('mouseenter', () => {
                this.animateUserAvatar(userAvatar);
            });
        }

        // Confirmación de logout
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.confirmLogout();
            });
        }
    }

    /**
     * Animar avatar de usuario
     */
    animateUserAvatar(avatar) {
        avatar.style.animation = 'none';
        requestAnimationFrame(() => {
            avatar.style.animation = 'avatarBounce 0.6s ease-out';
        });
    }

    /**
     * Confirmar cierre de sesión
     */
    confirmLogout() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Cerrar sesión?',
                text: '¿Estás seguro de que quieres salir de tu cuenta?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar',
                background: document.body.classList.contains('ithr-dark-mode') ? '#1f2937' : '#ffffff'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.performLogout();
                }
            });
        } else {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                this.performLogout();
            }
        }
    }

    /**
     * Ejecutar logout
     */
    performLogout() {
        // Mostrar loading
        this.showToast('Cerrando sesión...', 'info');
        
        // Simular delay y redirigir
        setTimeout(() => {
            window.location.href = '/logout';
        }, 1000);
    }

    /**
     * Configurar efectos de scroll
     */
    setupScrollEffects() {
        let lastScrollTop = 0;
        const header = document.querySelector('.tech-header');
        
        if (!header) return;

        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down
                header.style.transform = 'translateY(-10px)';
                header.style.opacity = '0.9';
            } else {
                // Scrolling up
                header.style.transform = 'translateY(0)';
                header.style.opacity = '1';
            }
            
            lastScrollTop = scrollTop;
        });
    }

    /**
     * Iniciar actualizaciones en tiempo real
     */
    startRealTimeUpdates() {
        // Actualizar notificaciones cada 30 segundos
        setInterval(() => {
            // Aquí se podría hacer una llamada AJAX para obtener nuevas notificaciones
            this.checkForNewNotifications();
        }, 30000);
    }

    /**
     * Verificar nuevas notificaciones (simulado)
     */
    checkForNewNotifications() {
        // Simulación de nueva notificación aleatoria
        if (Math.random() < 0.1) { // 10% de probabilidad
            const newNotification = {
                id: 'notif_' + Date.now(),
                type: 'info',
                icon: 'fas fa-info-circle',
                title: 'Nueva actividad',
                message: 'Hay nuevas actualizaciones disponibles',
                timestamp: Date.now(),
                read: false
            };

            this.notifications.unshift(newNotification);
            this.updateNotificationBadge();
            
            // Animación en el botón de notificaciones
            this.animateNotificationButton();
        }
    }

    /**
     * Animar botón de notificaciones
     */
    animateNotificationButton() {
        const btn = document.querySelector('.notifications-btn');
        if (btn) {
            btn.style.animation = 'none';
            requestAnimationFrame(() => {
                btn.style.animation = 'bellRing 0.5s ease-in-out';
            });
        }
    }

    /**
     * Calcular tiempo transcurrido
     */
    getTimeAgo(timestamp) {
        const now = Date.now();
        const diff = now - timestamp;
        
        const minutes = Math.floor(diff / (1000 * 60));
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        
        if (minutes < 1) return 'Ahora';
        if (minutes < 60) return `${minutes}m`;
        if (hours < 24) return `${hours}h`;
        return `${days}d`;
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
                timer: 3000,
                timerProgressBar: true,
                background: document.body.classList.contains('ithr-dark-mode') ? '#374151' : '#ffffff'
            });
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }

    /**
     * Cleanup al destruir
     */
    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
        
        document.removeEventListener('click', this.handleOutsideClick);
        console.log('🎯 TECH HOME Header: Destroyed');
    }
}

// Estilos CSS dinámicos para el panel de notificaciones
const notificationPanelStyles = `
    <style id="notification-panel-styles">
        .notifications-panel {
            position: absolute;
            top: calc(100% + 10px);
            right: 120px;
            width: 380px;
            max-height: 500px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            overflow: hidden;
        }

        .notifications-panel.show {
            opacity: 1;
            transform: translateY(0);
        }

        .notification-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #fafafa, #f5f5f5);
        }

        .notification-header h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mark-all-read {
            background: none;
            border: 1px solid #dc2626;
            color: #dc2626;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mark-all-read:hover {
            background: #dc2626;
            color: white;
        }

        .notification-list {
            max-height: 320px;
            overflow-y: auto;
            padding: 0.5rem 0;
        }

        .notification-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            transition: background 0.2s ease;
            position: relative;
        }

        .notification-item:hover {
            background: rgba(220, 38, 38, 0.02);
        }

        .notification-item.unread {
            background: rgba(59, 130, 246, 0.02);
            border-left: 3px solid #dc2626;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-icon.info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .notification-icon.success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .notification-icon.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

        .notification-content {
            flex: 1;
        }

        .notification-content h5 {
            margin: 0 0 0.3rem 0;
            font-size: 0.9rem;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.3;
        }

        .notification-content p {
            margin: 0 0 0.5rem 0;
            font-size: 0.8rem;
            color: #6b7280;
            line-height: 1.4;
        }

        .notification-time {
            font-size: 0.7rem;
            color: #9ca3af;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .notification-close {
            background: none;
            border: none;
            color: #9ca3af;
            padding: 0.2rem;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .notification-close:hover {
            background: rgba(220, 38, 38, 0.1);
            color: #dc2626;
        }

        .no-notifications {
            padding: 2rem;
            text-align: center;
            color: #9ca3af;
        }

        .no-notifications i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .notification-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            background: rgba(249, 250, 251, 0.5);
        }

        .view-all-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #dc2626;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .view-all-btn:hover {
            color: #b91c1c;
        }

        /* Animación para avatar */
        @keyframes avatarBounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Dark mode */
        body.ithr-dark-mode .notifications-panel {
            background: rgba(31, 41, 55, 0.98);
            border-color: rgba(75, 85, 99, 0.3);
        }

        body.ithr-dark-mode .notification-header {
            background: linear-gradient(135deg, #374151, #4b5563);
            border-color: rgba(75, 85, 99, 0.3);
        }

        body.ithr-dark-mode .notification-header h4 {
            color: #f9fafb;
        }

        body.ithr-dark-mode .notification-item {
            border-color: rgba(75, 85, 99, 0.2);
        }

        body.ithr-dark-mode .notification-item:hover {
            background: rgba(220, 38, 38, 0.05);
        }

        body.ithr-dark-mode .notification-content h5 {
            color: #f9fafb;
        }

        body.ithr-dark-mode .notification-content p {
            color: #d1d5db;
        }

        body.ithr-dark-mode .notification-footer {
            background: rgba(55, 65, 81, 0.5);
            border-color: rgba(75, 85, 99, 0.3);
        }
    </style>
`;

// Inyectar estilos si no existen
if (!document.querySelector('#notification-panel-styles')) {
    document.head.insertAdjacentHTML('beforeend', notificationPanelStyles);
}

// Inicialización automática cuando el DOM esté listo
let techHeader;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        techHeader = new TechHeader();
    });
} else {
    techHeader = new TechHeader();
}

// Exposición global para uso externo
window.TechHeader = TechHeader;
window.techHeader = techHeader;

// Cleanup al cerrar la página
window.addEventListener('beforeunload', () => {
    if (techHeader) {
        techHeader.destroy();
    }
});