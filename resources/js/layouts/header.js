// ============================================
// CLASE PRINCIPAL DEL HEADER COMPONENT
// ============================================
class TechHeaderComponent {
    constructor() {
        this.init();
    }

    // ============================================
    // INICIALIZACIÓN DEL COMPONENTE
    // ============================================
    init() {
        this.syncThemeWithSidebar();
        this.updateDateTime();
        this.startDateTimeUpdater();
        this.updateNotificationCount();
        this.initLogoutHandler();
        this.startSessionVerification();
        this.preventBackAfterLogout();
        this.initPageAnimations();
        this.listenForThemeChanges();

        console.log('Tech Header Component initialized');
    }

    // ============================================
    // SINCRONIZACIÓN DE TEMA CON SIDEBAR
    // ============================================
    syncThemeWithSidebar() {
        const savedTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
        if (savedTheme === 'dark') {
            document.body.classList.add('ithr-dark-mode');
        } else {
            document.body.classList.remove('ithr-dark-mode');
        }
    }

    listenForThemeChanges() {
        // Escuchar cambios de tema desde el sidebar
        document.addEventListener('themeChanged', () => {
            this.syncThemeWithSidebar();
        });

        // Monitorear cambios en localStorage
        window.addEventListener('storage', (e) => {
            if (e.key === 'ithrGlobalTheme') {
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
    // CONTADOR DE NOTIFICACIONES
    // ============================================
    updateNotificationCount() {
        // Obtener contador desde variable PHP o AJAX ligero
        const notificationCount = 0; // Por defecto 0, cambiar según tu lógica
        this.setNotificationCount(notificationCount);
    }

    // ============================================
    // MÉTODO PÚBLICO PARA ACTUALIZAR NOTIFICACIONES
    // ============================================
    setNotificationCount(count) {
        const badge = document.getElementById('notification-count');

        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    // ============================================
    // LÓGICA AVANZADA DE LOGOUT
    // ============================================
    initLogoutHandler() {
        const logoutBtn = document.getElementById('logoutBtn');

        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleLogout();
            });
        }
    }

    handleLogout() {
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutUrl = logoutBtn?.getAttribute('data-logout-url') || 'logout.php';

        // Confirmación de logout con mensaje personalizado
        const confirmMessage = '¿Estás seguro de que quieres cerrar sesión?\n\nSe perderán todos los datos no guardados.';

        if (confirm(confirmMessage)) {
            console.log('Iniciando proceso de logout...');
            console.log('Redirect URL:', logoutUrl);

            // Mostrar indicador de carga (opcional)
            this.showLogoutLoader();

            // Limpiar almacenamiento local
            this.clearLocalStorage();

            // Forzar limpieza de cache del navegador y redireccionar
            setTimeout(() => {
                window.location.href = logoutUrl + '?t=' + Date.now();
            }, 500);
        }
    }

    clearLocalStorage() {
        try {
            if (typeof(Storage) !== "undefined") {
                localStorage.clear();
                sessionStorage.clear();
                console.log('Storage local limpiado');
            }
        } catch (error) {
            console.warn('Error limpiando storage:', error);
        }
    }

    showLogoutLoader() {
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            const originalContent = logoutBtn.innerHTML;
            logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cerrando...';
            logoutBtn.style.pointerEvents = 'none';
        }
    }

    // ============================================
    // VERIFICACIÓN DE SESIÓN AUTOMÁTICA
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
                    'X-Requested-With': 'XMLHttpRequest'
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
            // No redirigir en caso de error de red
        }
    }

    handleSessionExpired() {
        alert('Tu sesión ha expirado. Serás redirigido al login.');
        this.clearLocalStorage();
        window.location.href = 'login.php?session_expired=1';
    }

    // ============================================
    // PREVENIR NAVEGACIÓN HACIA ATRÁS DESPUÉS DE LOGOUT
    // ============================================
    preventBackAfterLogout() {
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                console.log('Página cargada desde cache, recargando...');
                window.location.reload();
            }
        });

        // Prevenir cache de la página
        window.addEventListener('beforeunload', function() {
            // Forzar recarga en la próxima visita
        });
    }

    // ============================================
    // ANIMACIONES DE ENTRADA
    // ============================================
    initPageAnimations() {
        const animatedElements = document.querySelectorAll('.user-info, .welcome-section, .notifications-btn');

        animatedElements.forEach((element, index) => {
            if (element) {
                element.style.opacity = '0';
                element.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    element.style.transition = 'all 0.3s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    }

    // ============================================
    // MÉTODOS PÚBLICOS ADICIONALES
    // ============================================
    updateUserInfo(userData) {
        const nameElement = document.getElementById('user-name');
        const roleElement = document.getElementById('user-role');
        const emailElement = document.getElementById('user-email');

        if (nameElement && userData.nombre) {
            nameElement.textContent = userData.nombre + ' ' + (userData.apellido || '');
        }
        if (roleElement && userData.rol) {
            roleElement.textContent = userData.rol;
        }
        if (emailElement && userData.email) {
            emailElement.textContent = userData.email;
        }
    }

    setLogoutUrl(url) {
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.setAttribute('data-logout-url', url);
        }
    }
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
    updateNotifications: (count) => window.techHeader?.setNotificationCount(count),
    updateUserInfo: (userData) => window.techHeader?.updateUserInfo(userData),
    setLogoutUrl: (url) => window.techHeader?.setLogoutUrl(url),
    logout: () => window.techHeader?.handleLogout()
};
