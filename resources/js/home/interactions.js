/**
 * ============================================
 * TECH HOME - Interacciones Avanzadas para Welcome
 * Funcionalidades específicas de interacción para welcome.blade.php
 * ============================================
 */

// Extensión del namespace TechHome para interacciones
TechHome.Interactions = TechHome.Interactions || {};

// Sistema de Interacciones Avanzadas
TechHome.Interactions.init = function() {
    this.initCardInteractions();
    this.initSearchFunctionality();
    this.initModalSystem();
    this.initTooltips();
    this.initDragAndDrop();
    this.initKeyboardShortcuts();
    
    console.log('🎮 Sistema de interacciones avanzadas iniciado');
};

// Interacciones de Tarjetas Mejoradas
TechHome.Interactions.initCardInteractions = function() {
    const cards = document.querySelectorAll('.quick-action-card, .library-card, .component-card');
    
    cards.forEach(card => {
        // Efecto de inclinación 3D
        card.addEventListener('mousemove', function(e) {
            if (window.innerWidth < 768) return; // Desactivar en móviles
            
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            this.style.transform = `
                perspective(1000px) 
                rotateX(${rotateX}deg) 
                rotateY(${rotateY}deg) 
                translateZ(10px)
            `;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
        
        // Click feedback mejorado
        card.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON') {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            }
        });
        
        // Doble click para vista rápida
        card.addEventListener('dblclick', function(e) {
            e.preventDefault();
            const title = this.querySelector('.quick-action-title, .library-title, .component-title')?.textContent;
            TechHome.Interactions.showQuickView(title, this);
        });
    });
};

// Sistema de Búsqueda Inteligente
TechHome.Interactions.initSearchFunctionality = function() {
    // Crear barra de búsqueda si no existe
    const header = document.querySelector('.tech-home-header');
    if (header && !document.getElementById('tech-search')) {
        const searchContainer = document.createElement('div');
        searchContainer.style.cssText = `
            position: relative;
            margin-left: auto;
            margin-right: 20px;
        `;
        
        searchContainer.innerHTML = `
            <input type="text" 
                   id="tech-search" 
                   placeholder="Buscar en TECH HOME..." 
                   style="
                       padding: 10px 40px 10px 15px;
                       border: 2px solid #e5e7eb;
                       border-radius: 25px;
                       width: 250px;
                       font-size: 14px;
                       transition: all 0.3s ease;
                   ">
            <i class="fas fa-search" style="
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: #6b7280;
                pointer-events: none;
            "></i>
        `;
        
        header.appendChild(searchContainer);
        
        // Funcionalidad de búsqueda
        const searchInput = searchContainer.querySelector('#tech-search');
        
        searchInput.addEventListener('focus', function() {
            this.style.borderColor = '#dc2626';
            this.style.boxShadow = '0 0 0 3px rgba(220, 38, 38, 0.1)';
            this.style.width = '300px';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.borderColor = '#e5e7eb';
            this.style.boxShadow = '';
            this.style.width = '250px';
        });
        
        searchInput.addEventListener('input', TechHome.utils.debounce((e) => {
            this.performSearch(e.target.value);
        }, 300));
        
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.performSearch(e.target.value);
            }
        });
    }
};

// Realizar Búsqueda
TechHome.Interactions.performSearch = function(query) {
    if (!query.trim()) {
        this.clearSearchHighlights();
        return;
    }
    
    const searchableElements = document.querySelectorAll(`
        .quick-action-title, 
        .library-title, 
        .component-title,
        .quick-action-description,
        .library-description,
        .component-description
    `);
    
    let foundResults = 0;
    
    searchableElements.forEach(element => {
        const text = element.textContent.toLowerCase();
        const card = element.closest('.quick-action-card, .library-card, .component-card');
        
        if (text.includes(query.toLowerCase())) {
            this.highlightSearchResult(element, query);
            if (card) {
                card.style.display = 'block';
                card.classList.add('search-match');
            }
            foundResults++;
        } else {
            this.removeSearchHighlight(element);
            if (card) {
                card.style.display = 'none';
                card.classList.remove('search-match');
            }
        }
    });
    
    // Mostrar resultados
    const message = foundResults > 0 
        ? `${foundResults} resultado${foundResults !== 1 ? 's' : ''} encontrado${foundResults !== 1 ? 's' : ''}`
        : 'No se encontraron resultados';
    
    TechHome.showNotification(message, foundResults > 0 ? 'success' : 'warning', 3000);
};

// Resaltar Resultados de Búsqueda
TechHome.Interactions.highlightSearchResult = function(element, query) {
    const text = element.textContent;
    const regex = new RegExp(`(${query})`, 'gi');
    const highlightedText = text.replace(regex, '<mark style="background: #fef3c7; color: #92400e; padding: 2px 4px; border-radius: 3px;">$1</mark>');
    element.innerHTML = highlightedText;
};

// Remover Resaltado de Búsqueda
TechHome.Interactions.removeSearchHighlight = function(element) {
    const text = element.textContent; // Esto elimina automáticamente las etiquetas HTML
    element.textContent = text;
};

// Limpiar Resaltados de Búsqueda
TechHome.Interactions.clearSearchHighlights = function() {
    const cards = document.querySelectorAll('.quick-action-card, .library-card, .component-card');
    cards.forEach(card => {
        card.style.display = 'block';
        card.classList.remove('search-match');
    });
    
    const highlightedElements = document.querySelectorAll('mark');
    highlightedElements.forEach(mark => {
        const parent = mark.parentNode;
        parent.replaceChild(document.createTextNode(mark.textContent), mark);
        parent.normalize();
    });
};

// Sistema Modal
TechHome.Interactions.initModalSystem = function() {
    // Crear modal container si no existe
    if (!document.getElementById('tech-modal-container')) {
        const modalContainer = document.createElement('div');
        modalContainer.id = 'tech-modal-container';
        modalContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(5px);
        `;
        document.body.appendChild(modalContainer);
    }
};

// Mostrar Vista Rápida
TechHome.Interactions.showQuickView = function(title, cardElement) {
    const modalContainer = document.getElementById('tech-modal-container');
    if (!modalContainer) return;
    
    const modalContent = document.createElement('div');
    modalContent.style.cssText = `
        background: white;
        border-radius: 16px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        padding: 30px;
        position: relative;
        transform: scale(0.7);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    `;
    
    // Extraer información de la tarjeta
    const description = cardElement.querySelector('.quick-action-description, .library-description, .component-description')?.textContent || '';
    const stats = cardElement.querySelector('.stat-number')?.textContent || '';
    const icon = cardElement.querySelector('.quick-action-icon, .library-icon, .component-icon')?.innerHTML || '';
    
    modalContent.innerHTML = `
        <button onclick="TechHome.Interactions.closeModal()" 
                style="position: absolute; top: 15px; right: 20px; background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;">
            ×
        </button>
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="width: 80px; height: 80px; margin: 0 auto 20px; border-radius: 16px; background: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white;">
                ${icon}
            </div>
            <h2 style="color: #111827; margin-bottom: 10px; font-size: 1.8rem;">${title}</h2>
            <p style="color: #6b7280; line-height: 1.6;">${description}</p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 25px;">
            <div style="text-align: center; padding: 15px; background: #fef2f2; border-radius: 12px;">
                <div style="font-size: 1.5rem; font-weight: 800; color: #dc2626;">${stats}</div>
                <div style="font-size: 0.9rem; color: #6b7280;">Elementos</div>
            </div>
            <div style="text-align: center; padding: 15px; background: #f0fdf4; border-radius: 12px;">
                <div style="font-size: 1.5rem; font-weight: 800; color: #16a34a;">Online</div>
                <div style="font-size: 0.9rem; color: #6b7280;">Estado</div>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button onclick="TechHome.Interactions.accessSection('${title}')" 
                    style="flex: 1; padding: 12px 20px; background: #dc2626; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                Acceder
            </button>
            <button onclick="TechHome.Interactions.closeModal()" 
                    style="flex: 1; padding: 12px 20px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                Cerrar
            </button>
        </div>
    `;
    
    modalContainer.innerHTML = '';
    modalContainer.appendChild(modalContent);
    modalContainer.style.display = 'flex';
    
    // Animar entrada
    setTimeout(() => {
        modalContent.style.transform = 'scale(1)';
        modalContent.style.opacity = '1';
    }, 50);
};

// Cerrar Modal
TechHome.Interactions.closeModal = function() {
    const modalContainer = document.getElementById('tech-modal-container');
    if (modalContainer) {
        const modalContent = modalContainer.querySelector('div');
        if (modalContent) {
            modalContent.style.transform = 'scale(0.7)';
            modalContent.style.opacity = '0';
        }
        
        setTimeout(() => {
            modalContainer.style.display = 'none';
        }, 300);
    }
};

// Acceder a Sección
TechHome.Interactions.accessSection = function(section) {
    this.closeModal();
    TechHome.showNotification(`Accediendo a ${section}...`, 'info');
    TechHome.simulateNavigation(section);
};

// Sistema de Tooltips
TechHome.Interactions.initTooltips = function() {
    const elementsWithTooltips = document.querySelectorAll('[data-tooltip]');
    
    elementsWithTooltips.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const tooltip = this.getAttribute('data-tooltip');
            if (!tooltip) return;
            
            const tooltipElement = document.createElement('div');
            tooltipElement.className = 'tech-tooltip';
            tooltipElement.textContent = tooltip;
            tooltipElement.style.cssText = `
                position: absolute;
                background: #111827;
                color: white;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 12px;
                white-space: nowrap;
                z-index: 9999;
                opacity: 0;
                transition: opacity 0.2s ease;
                pointer-events: none;
            `;
            
            document.body.appendChild(tooltipElement);
            
            // Posicionar tooltip
            const rect = this.getBoundingClientRect();
            tooltipElement.style.left = rect.left + (rect.width / 2) - (tooltipElement.offsetWidth / 2) + 'px';
            tooltipElement.style.top = rect.top - tooltipElement.offsetHeight - 10 + 'px';
            
            setTimeout(() => {
                tooltipElement.style.opacity = '1';
            }, 50);
            
            this._tooltip = tooltipElement;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.style.opacity = '0';
                setTimeout(() => {
                    if (this._tooltip && this._tooltip.parentNode) {
                        this._tooltip.parentNode.removeChild(this._tooltip);
                    }
                    this._tooltip = null;
                }, 200);
            }
        });
    });
};

// Atajos de Teclado
TechHome.Interactions.initKeyboardShortcuts = function() {
    document.addEventListener('keydown', (e) => {
        // Ctrl/Cmd + K para búsqueda
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.getElementById('tech-search');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Ctrl/Cmd + 1-4 para pestañas
        if ((e.ctrlKey || e.metaKey) && ['1', '2', '3', '4'].includes(e.key)) {
            e.preventDefault();
            const tabIndex = parseInt(e.key) - 1;
            const tabButton = document.querySelectorAll('.tab-button')[tabIndex];
            if (tabButton) {
                tabButton.click();
            }
        }
        
        // ESC para cerrar modal y limpiar búsqueda
        if (e.key === 'Escape') {
            this.closeModal();
            const searchInput = document.getElementById('tech-search');
            if (searchInput && searchInput.value) {
                searchInput.value = '';
                this.clearSearchHighlights();
            }
        }
    });
};

// Drag and Drop (para futuras funcionalidades)
TechHome.Interactions.initDragAndDrop = function() {
    const draggableCards = document.querySelectorAll('.quick-action-card, .library-card, .component-card');
    
    draggableCards.forEach(card => {
        card.draggable = true;
        
        card.addEventListener('dragstart', function(e) {
            this.style.opacity = '0.7';
            e.dataTransfer.setData('text/plain', this.outerHTML);
        });
        
        card.addEventListener('dragend', function() {
            this.style.opacity = '1';
        });
    });
    
    // Crear zona de drop para favoritos
    const header = document.querySelector('.tech-home-header');
    if (header) {
        const favoriteZone = document.createElement('div');
        favoriteZone.innerHTML = '<i class="fas fa-star"></i> Favoritos';
        favoriteZone.style.cssText = `
            padding: 8px 15px;
            background: #fef3c7;
            color: #d97706;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
        `;
        
        favoriteZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            favoriteZone.style.background = '#fbbf24';
        });
        
        favoriteZone.addEventListener('dragleave', () => {
            favoriteZone.style.background = '#fef3c7';
        });
        
        favoriteZone.addEventListener('drop', (e) => {
            e.preventDefault();
            favoriteZone.style.background = '#fef3c7';
            TechHome.showNotification('Agregado a favoritos', 'success');
        });
        
        header.appendChild(favoriteZone);
    }
};

// Inicializar interacciones cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Pequeño delay para asegurar que TechHome esté completamente iniciado
    setTimeout(() => {
        TechHome.Interactions.init();
    }, 500);
});