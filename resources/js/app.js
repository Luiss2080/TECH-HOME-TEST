// Archivo principal de JavaScript
import './bootstrap';

// Importar módulos de autenticación
import './auth/login.js';
import './auth/register.js';
import './auth/forgot-password.js';

// Configuración global
window.addEventListener('DOMContentLoaded', function() {
    console.log('TECH-HOME App iniciada');
    
    // Inicializar componentes globales
    initializeGlobalComponents();
});

function initializeGlobalComponents() {
    // Manejar alertas automáticas
    const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
    alerts.forEach(alert => {
        const delay = parseInt(alert.dataset.autoDismiss) || 5000;
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, delay);
    });

    // Manejar tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
    
    // Manejar formularios con confirmación
    const confirmedForms = document.querySelectorAll('form[data-confirm]');
    confirmedForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const message = this.dataset.confirm;
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
}

function showTooltip(e) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = e.target.dataset.tooltip;
    document.body.appendChild(tooltip);
    
    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + 'px';
    tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

// Funciones de utilidad global
window.AppUtils = {
    // Función para mostrar notificaciones
    showNotification: function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.innerHTML = `
            <span>${message}</span>
            <button type="button" class="close" onclick="this.parentElement.remove()">×</button>
        `;
        
        const container = document.querySelector('.notification-container') || document.body;
        container.appendChild(notification);
        
        // Auto-dismiss después de 5 segundos
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    },
    
    // Función para hacer peticiones AJAX
    ajax: function(url, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        };
        
        const config = { ...defaults, ...options };
        
        if (config.method !== 'GET' && config.data) {
            config.body = JSON.stringify(config.data);
        }
        
        return fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('Ajax error:', error);
                throw error;
            });
    }
};