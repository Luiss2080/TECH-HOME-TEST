// ============================================
// HEADER TECH HOME - Instituto de Robótica
// ============================================

class TechHeaderComponent {
    constructor() {
        this.notificationsDropdown = null;
        this.profileDropdown = null;
        this.notificationsBtn = null;
        this.userProfile = null;
        this.init();
    }

    // ============================================
    // INICIALIZACIÓN DEL COMPONENTE
    // ============================================
    init() {
        this.initializeElements();
        this.syncThemeWithSidebar();
        this.updateDateTime();
        this.startDateTimeUpdater();
        this.initDropdowns();
        this.initNotifications();
        this.initUserProfile();
        this.initPageAnimations();
        this.listenForThemeChanges();
        this.startSessionVerification();
        this.preventBackAfterLogout();
        this.initHeaderEffects();

        console.log('✅ Tech Header Component inicializado correctamente');
    }

    // ============================================
    // INICIALIZAR ELEMENTOS DOM
    // ============================================
    initializeElements() {
        this.notificationsDropdown = document.getElementById('notificationsDropdown');
        this.profileDropdown = document.getElementById('profileDropdown');
        this.notificationsBtn = document.getElementById('notificationsBtn');
        this.userProfile = document.getElementById('userProfile');
    }

    // ============================================
    // SINCRONIZACIÓN DE TEMA CON SIDEBAR
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
    // FECHA Y HORA EN TIEMPO REAL
    // ============================================
    updateDateTime() {
        const now = new Date();

        const dateOptions = {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        };

        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };

        const formattedDate = now.toLocaleDateString('es-ES', dateOptions);
        const formattedTime = now.toLocaleTimeString('es-ES', timeOptions);

        const dateElement = document.getElementById('current-date');
        const timeElement = document.getElementById('current-time');

        if (dateElement) dateElement.textContent = formattedDate;
        if (timeElement) timeElement.textContent = formattedTime;
    }

    startDateTimeUpdater() {
        setInterval(() => this.updateDateTime(), 1000);
    }

    // ============================================
    // SISTEMA DE DROPDOWNS
    // ============================================
    initDropdowns() {
        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (this.notificationsDropdown && !this.notificationsBtn?.contains(e.target) && !this.notificationsDropdown.contains(e.target)) {
                this.closeNotificationsDropdown();
            }
            
            if (this.profileDropdown && !this.userProfile?.contains(e.target) && !this.profileDropdown.contains(e.target)) {
                this.closeProfileDropdown();
            }
        });

        // Cerrar con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllDropdowns();
            }
        });
    }

    closeAllDropdowns() {
        this.closeNotificationsDropdown();
        this.closeProfileDropdown();
    }

    // ============================================
    // SISTEMA DE NOTIFICACIONES
    // ============================================
    initNotifications() {
        if (this.notificationsBtn) {
            this.notificationsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleNotificationsDropdown();
            });
        }

        // Marcar todas como leídas
        const markAllRead = document.querySelector('.mark-all-read');
        if (markAllRead) {
            markAllRead.addEventListener('click', () => {
                this.markAllNotificationsAsRead();
            });
        }

        // Manejar clics en notificaciones individuales
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', (e) => {
                this.handleNotificationClick(e, item);
            });
        });

        // Actualizar contador inicial
        this.updateNotificationCount();
    }

    toggleNotificationsDropdown() {
        if (!this.notificationsDropdown) return;

        const isOpen = this.notificationsDropdown.classList.contains('show');
        
        this.closeProfileDropdown(); // Cerrar otro dropdown
        
        if (isOpen) {
            this.closeNotificationsDropdown();
        } else {
            this.openNotificationsDropdown();
        }
    }

    openNotificationsDropdown() {
        if (this.notificationsDropdown) {
            this.notificationsDropdown.classList.add('show');
            this.notificationsBtn?.classList.add('active');
        }
    }

    closeNotificationsDropdown() {
        if (this.notificationsDropdown) {
            this.notificationsDropdown.classList.remove('show');
            this.notificationsBtn?.classList.remove('active');
        }
    }

    updateNotificationCount(count = null) {
        const badge = document.getElementById('notificationCount');
        if (badge) {
            if (count === null) {
                // Obtener contador desde el servidor o usar valor por defecto
                count = this.getNotificationCountFromServer();
            }
            
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    getNotificationCountFromServer() {
        // Aquí podrías hacer una llamada AJAX para obtener el contador real
        // Por ahora retornamos un valor de ejemplo
        return 3;
    }

    markAllNotificationsAsRead() {
        // Implementar lógica para marcar todas las notificaciones como leídas
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.classList.add('read');
        });
        
        this.updateNotificationCount(0);
        
        // Aquí harías la llamada AJAX al servidor
        this.sendMarkAllReadRequest();
    }

    handleNotificationClick(e, item) {
        e.stopPropagation();
        
        // Marcar como leída
        item.classList.add('read');
        
        // Obtener datos de la notificación
        const notificationData = this.extractNotificationData(item);
        
        // Procesar la notificación
        this.processNotification(notificationData);
        
        // Cerrar dropdown
        this.closeNotificationsDropdown();
    }

    extractNotificationData(item) {
        return {
            content: item.querySelector('.notification-content p')?.textContent,
            time: item.querySelector('.notification-time')?.textContent,
            type: item.querySelector('.notification-icon i')?.className
        };
    }

    processNotification(data) {
        console.log('Procesando notificación:', data);
        // Implementar lógica específica según el tipo de notificación
    }

    sendMarkAllReadRequest() {
        // Implementar llamada AJAX
        console.log('Marcando todas las notificaciones como leídas...');
    }

    // ============================================
    // PERFIL DE USUARIO
    // ============================================
    initUserProfile() {
        if (this.userProfile) {
            this.userProfile.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleProfileDropdown();
            });
        }

        // Manejar logout
        const logoutForm = document.querySelector('.logout-form');
        if (logoutForm) {
            logoutForm.addEventListener('submit', (e) => {
                this.handleLogout(e);
            });
        }

        // Efectos hover en elementos del dropdown
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
            });

            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
    }

    toggleProfileDropdown() {
        if (!this.profileDropdown || !this.userProfile) return;

        const isOpen = this.profileDropdown.classList.contains('show');
        
        this.closeNotificationsDropdown(); // Cerrar otro dropdown
        
        if (isOpen) {
            this.closeProfileDropdown();
        } else {
            this.openProfileDropdown();
        }
    }

    openProfileDropdown() {
        if (this.profileDropdown && this.userProfile) {
            this.profileDropdown.classList.add('show');
            this.userProfile.classList.add('active');
        }
    }

    closeProfileDropdown() {
        if (this.profileDropdown && this.userProfile) {
            this.profileDropdown.classList.remove('show');
            this.userProfile.classList.remove('active');
        }
    }

    // ============================================
    // LÓGICA DE LOGOUT MEJORADA
    // ============================================
    handleLogout(e) {
        e.preventDefault();
        
        const confirmMessage = '¿Estás seguro de que quieres cerrar sesión?\n\nSe perderán todos los datos no guardados.';
        
        if (confirm(confirmMessage)) {
            console.log('Iniciando proceso de logout...');
            
            // Mostrar indicador de carga
            this.showLogoutLoader();
            
            // Limpiar almacenamiento local
            this.clearLocalStorage();
            
            // Enviar formulario después de un breve delay
            setTimeout(() => {
                e.target.submit();
            }, 500);
        }
    }

    showLogoutLoader() {
        const logoutBtn = document.querySelector('.logout-item');
        if (logoutBtn) {
            const originalContent = logoutBtn.innerHTML;
            logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Cerrando sesión...</span>';
            logoutBtn.style.pointerEvents = 'none';
        }
    }

    clearLocalStorage() {
        try {
            if (typeof(Storage) !== "undefined") {
                // Mantener el tema pero limpiar otros datos
                const savedTheme = localStorage.getItem('techHomeTheme');
                localStorage.clear();
                if (savedTheme) {
                    localStorage.setItem('techHomeTheme', savedTheme);
                }
                sessionStorage.clear();
                console.log('Storage local limpiado');
            }
        } catch (error) {
            console.warn('Error limpiando storage:', error);
        }
    }

    // ============================================
    // VERIFICACIÓN DE SESIÓN
    // ============================================
    startSessionVerification() {
        // Verificar sesión cada 30 segundos
        setInterval(() => {
            this.verifySession();
        }, 30000);
    }

    async verifySession() {
        try {
            const response = await fetch('/verify-session', {
                method: 'GET',
                cache: 'no-cache',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (response.ok) {
                const data = await response.json();

                if (!data.authenticated) {
                    console.log('Sesión expirada, redirigiendo a login...');
                    this.handleSessionExpired();
                }
            }
        } catch (error) {
            console.warn('Error verificando sesión:', error);
        }
    }

    handleSessionExpired() {
        alert('Tu sesión ha expirado. Serás redirigido al login.');
        this.clearLocalStorage();
        window.location.href = '/login?session_expired=1';
    }

    // ============================================
    // PREVENIR NAVEGACIÓN HACIA ATRÁS
    // ============================================
    preventBackAfterLogout() {
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                console.log('Página cargada desde cache, recargando...');
                window.location.reload();
            }
        });
    }

    // ============================================
    // ANIMACIONES Y EFECTOS
    // ============================================
    initPageAnimations() {
        const animatedElements = document.querySelectorAll('.user-info, .datetime-section, .notifications-btn');

        animatedElements.forEach((element, index) => {
            if (element) {
                element.style.opacity = '0';
                element.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    element.style.transition = 'all 0.4s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100 + 200);
            }
        });
    }

    initHeaderEffects() {
        const header = document.querySelector('.tech-header');
        if (header) {
            // Efecto de partículas en el header
            this.createHeaderParticles();
        }
    }

    createHeaderParticles() {
        setInterval(() => {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: 3px;
                height: 3px;
                background: rgba(220, 38, 38, 0.4);
                border-radius: 50%;
                pointer-events: none;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation: headerParticle 4s ease-out forwards;
                z-index: 1;
            `;

            const headerBackground = document.querySelector('.header-background');
            if (headerBackground) {
                headerBackground.appendChild(particle);
                setTimeout(() => {
                    if (particle.parentNode) {
                        particle.remove();
                    }
                }, 4000);
            }
        }, 3000);
    }

    // ============================================
    // MÉTODOS PÚBLICOS
    // ============================================
    updateUserInfo(userData) {
        const nameElement = document.querySelector('.user-name');
        const roleElement = document.querySelector('.user-role');
        
        if (nameElement && userData.nombre) {
            nameElement.textContent = userData.nombre + ' ' + (userData.apellido || '');
        }
        if (roleElement && userData.rol) {
            roleElement.textContent = userData.rol;
        }
    }

    addNotification(notification) {
        const notificationsList = document.querySelector('.notifications-list');
        if (notificationsList) {
            const notificationElement = this.createNotificationElement(notification);
            notificationsList.insertBefore(notificationElement, notificationsList.firstChild);
            
            // Actualizar contador
            const currentCount = this.getNotificationCountFromServer();
            this.updateNotificationCount(currentCount + 1);
        }
    }

    createNotificationElement(notification) {
        const div = document.createElement('div');
        div.className = 'notification-item';
        div.innerHTML = `
            <i class="${notification.icon} notification-icon"></i>
            <div class="notification-content">
                <p>${notification.message}</p>
                <span class="notification-time">${notification.time || 'Ahora'}</span>
            </div>
        `;
        
        div.addEventListener('click', (e) => {
            this.handleNotificationClick(e, div);
        });
        
        return div;
    }
}

// ============================================
// AGREGAR ESTILOS CSS NECESARIOS
// ============================================
if (!document.getElementById('header-particle-animations')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'header-particle-animations';
    styleSheet.textContent = `
        @keyframes headerParticle {
            0% {
                transform: scale(0) translateY(0);
                opacity: 0.8;
            }
            50% {
                transform: scale(1) translateY(-20px);
                opacity: 1;
            }
            100% {
                transform: scale(0) translateY(-40px);
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
    window.techHeader = new TechHeaderComponent();
});

// ============================================
// API PÚBLICA SIMPLIFICADA
// ============================================
window.TechHeader = {
    updateNotifications: (count) => window.techHeader?.updateNotificationCount(count),
    addNotification: (notification) => window.techHeader?.addNotification(notification),
    updateUserInfo: (userData) => window.techHeader?.updateUserInfo(userData),
    closeDropdowns: () => window.techHeader?.closeAllDropdowns(),
    logout: () => window.techHeader?.handleLogout({ preventDefault: () => {}, target: { submit: () => {} } })
};