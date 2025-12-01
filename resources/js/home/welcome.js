/**
 * JavaScript específico para la vista Home de TECH HOME
 * 
 * Este archivo contiene toda la funcionalidad JavaScript necesaria para
 * la vista principal de bienvenida del sistema TECH HOME, incluyendo:
 * - Manejo de tabs de información
 * - Reloj en tiempo real
 * - Navegación entre secciones
 * - Funcionalidad de laboratorios virtuales  
 * - Sistema de notificaciones
 * - Animaciones y efectos visuales
 * - Observadores de intersección para animaciones
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // SECCIÓN: MANEJO DE TABS DE INFORMACIÓN
    // ==========================================
    
    /**
     * Configura el sistema de tabs para cambiar entre diferentes paneles de información
     */
    function initializeTabs() {
        document.querySelectorAll('.crud-info-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remover clase activa de todos los tabs
                document.querySelectorAll('.crud-info-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.crud-info-pane').forEach(p => p.classList.remove('active'));
                
                // Activar tab seleccionado
                this.classList.add('active');
                const targetPane = document.getElementById(this.dataset.tab);
                if (targetPane) {
                    targetPane.classList.add('active');
                }
            });
        });
    }

    // ==========================================
    // SECCIÓN: RELOJ EN TIEMPO REAL
    // ==========================================
    
    /**
     * Actualiza el reloj en tiempo real en el header principal
     */
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const dateString = now.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const clockElement = document.getElementById('techHomeTime');
        if (clockElement) {
            clockElement.innerHTML = `
                <div class="time">${timeString}</div>
                <div class="date">${dateString}</div>
            `;
        }
    }
    
    /**
     * Inicializa el reloj y programa actualizaciones cada segundo
     */
    function initializeClock() {
        updateClock();
        setInterval(updateClock, 1000);
    }

    // ==========================================
    // SECCIÓN: NAVEGACIÓN ENTRE SECCIONES
    // ==========================================
    
    /**
     * Información detallada de cada sección para mostrar en modales/alertas
     */
    const sectionInfo = {
        // Biblioteca Digital
        'robotica-libros': {
            titulo: 'Robótica Avanzada - Biblioteca Digital',
            descripcion: 'Colección especializada en robótica móvil, manipuladores industriales y sistemas autónomos.',
            caracteristicas: [
                '• 845 títulos especializados en robótica',
                '• Desde fundamentos hasta aplicaciones industriales',
                '• Incluye casos de estudio y proyectos prácticos',
                '• Autores reconocidos internacionalmente',
                '• Actualizaciones constantes con nuevas tecnologías'
            ]
        },
        'ia-libros': {
            titulo: 'Inteligencia Artificial - Recursos Académicos',
            descripcion: 'Biblioteca completa sobre machine learning, deep learning y sistemas inteligentes.',
            caracteristicas: [
                '• 632 libros especializados en IA',
                '• Algoritmos de aprendizaje automático',
                '• Redes neuronales y deep learning',
                '• Procesamiento de lenguaje natural',
                '• Aplicaciones en robótica e industria'
            ]
        },
        'programacion-libros': {
            titulo: 'Programación Avanzada - Sistemas Embebidos',
            descripcion: 'Recursos especializados en lenguajes de programación para robótica y sistemas embebidos.',
            caracteristicas: [
                '• 589 títulos de programación especializada',
                '• C/C++, Python, ROS y MATLAB',
                '• Programación de microcontroladores',
                '• Algoritmos para sistemas en tiempo real',
                '• Desarrollo de firmware especializado'
            ]
        },
        'control-libros': {
            titulo: 'Ingeniería de Control - Teoría y Práctica',
            descripcion: 'Biblioteca especializada en sistemas de control automático y teoría de control.',
            caracteristicas: [
                '• 781 libros de teoría de control',
                '• Control clásico y moderno',
                '• Sistemas de control digital',
                '• Control adaptativo y robusto',
                '• Aplicaciones en robótica industrial'
            ]
        },
        
        // Centro de Componentes
        'microcontroladores': {
            titulo: 'Microcontroladores y Sistemas Embebidos',
            descripcion: 'Inventario completo de microcontroladores, SBCs y sistemas de desarrollo.',
            caracteristicas: [
                '• Arduino (Uno, Mega, Nano, ESP32)',
                '• Raspberry Pi (4B, Zero, Compute Module)',
                '• STM32 y microcontroladores ARM',
                '• Kits de desarrollo y shields especializados',
                '• Sistemas de prototipado rápido'
            ]
        },
        'sensores': {
            titulo: 'Sensores Avanzados para Robótica',
            descripcion: 'Amplia gama de sensores especializados para percepción robótica.',
            caracteristicas: [
                '• Cámaras RGB-D y sistemas de visión',
                '• Sensores LIDAR y ultrasónicos',
                '• IMUs y sensores de orientación',
                '• Sensores táctiles y de fuerza',
                '• Sensores ambientales y químicos'
            ]
        },
        'actuadores': {
            titulo: 'Actuadores y Motores Especializados',
            descripcion: 'Sistema completo de actuadores para aplicaciones robóticas.',
            caracteristicas: [
                '• Servomotores de alta precisión',
                '• Motores paso a paso y lineales',
                '• Actuadores neumáticos e hidráulicos',
                '• Sistemas de tracción y locomoción',
                '• Controladores y drivers especializados'
            ]
        },
        'alimentacion': {
            titulo: 'Sistemas de Alimentación Robótica',
            descripcion: 'Soluciones completas de alimentación para sistemas robóticos.',
            caracteristicas: [
                '• Baterías LiPo y Li-ion especializadas',
                '• Fuentes de alimentación conmutadas',
                '• Sistemas de carga inalámbrica',
                '• Reguladores de voltaje especializados',
                '• Sistemas de gestión de energía'
            ]
        },
        
        // Cursos Especializados
        'robotica-basica': {
            titulo: 'Curso: Robótica Básica',
            descripcion: 'Introducción completa al mundo de la robótica, desde conceptos fundamentales hasta proyectos prácticos.',
            caracteristicas: [
                '• 40 horas de formación intensiva',
                '• Programación básica de Arduino',
                '• Construcción de robots móviles',
                '• Sensores y actuadores básicos',
                '• Proyecto final certificado'
            ]
        },
        'machine-learning': {
            titulo: 'Curso: Machine Learning Aplicado',
            descripcion: 'Formación avanzada en algoritmos de aprendizaje automático con aplicaciones robóticas.',
            caracteristicas: [
                '• 60 horas de contenido especializado',
                '• Python, TensorFlow y PyTorch',
                '• Algoritmos supervisados y no supervisados',
                '• Redes neuronales profundas',
                '• Proyectos con datasets reales'
            ]
        },
        'vision-artificial': {
            titulo: 'Curso: Visión Artificial Avanzada',
            descripcion: 'Curso especializado en procesamiento de imágenes y sistemas de visión para robótica.',
            caracteristicas: [
                '• 80 horas de formación avanzada',
                '• OpenCV y bibliotecas especializadas',
                '• Detección y reconocimiento de objetos',
                '• Sistemas de navegación visual',
                '• Integración con sistemas robóticos'
            ]
        },
        'iot-automatizacion': {
            titulo: 'Curso: IoT y Automatización Industrial',
            descripcion: 'Formación en Internet de las Cosas aplicado a la automatización y robótica.',
            caracteristicas: [
                '• 50 horas de contenido práctico',
                '• Protocolos IoT (MQTT, CoAP, LoRaWAN)',
                '• Sensores inalámbricos y conectividad',
                '• Plataformas cloud y edge computing',
                '• Proyectos de automatización industrial'
            ]
        },
        
        // Secciones generales
        'biblioteca': {
            titulo: 'Biblioteca Digital TECH HOME',
            descripcion: 'Accede a nuestra extensa colección de recursos académicos especializados en robótica, inteligencia artificial, programación y tecnología avanzada.',
            caracteristicas: [
                '• Más de 2,800 libros especializados en formato digital',
                '• Recursos actualizados constantemente',
                '• Acceso 24/7 desde cualquier dispositivo',
                '• Sistema de búsqueda avanzada por temas',
                '• Biblioteca de proyectos y casos de estudio'
            ]
        },
        'componentes': {
            titulo: 'Centro de Componentes Robóticos',
            descripcion: 'Descubre nuestro inventario completo de componentes, sensores, actuadores y hardware especializado para desarrollo robótico.',
            caracteristicas: [
                '• Más de 1,500 componentes disponibles',
                '• Sensores Arduino, Raspberry Pi y microcontroladores',
                '• Actuadores y motores especializados',
                '• Sistemas de visión artificial',
                '• Hardware para IA y machine learning'
            ]
        },
        'cursos': {
            titulo: 'Cursos de Robótica e IA',
            descripcion: 'Explora nuestro catálogo de cursos especializados, desde nivel básico hasta avanzado, impartidos por expertos en tecnología.',
            caracteristicas: [
                '• 45 cursos activos en diferentes niveles',
                '• Programación de robots y sistemas autónomos',
                '• Inteligencia artificial y machine learning',
                '• Visión por computadora y procesamiento de imágenes',
                '• Certificaciones reconocidas internacionalmente'
            ]
        },
        'proyectos': {
            titulo: 'Proyectos de Innovación',
            descripcion: 'Conoce los proyectos innovadores que desarrollamos, desde prototipos académicos hasta soluciones industriales reales.',
            caracteristicas: [
                '• 128 proyectos en desarrollo activo',
                '• Colaboraciones con empresas tecnológicas',
                '• Robots autónomos y sistemas inteligentes',
                '• Soluciones IoT y automatización industrial',
                '• Investigación en IA aplicada'
            ]
        }
    };

    /**
     * Muestra información detallada sobre una sección específica
     * @param {string} seccion - Identificador de la sección
     */
    window.showInfo = function(seccion) {
        const info = sectionInfo[seccion];
        if (info) {
            alert(`${info.titulo}\n\n${info.descripcion}\n\nCaracterísticas principales:\n${info.caracteristicas.join('\n')}\n\n¡Regístrate para acceder a todo el contenido!`);
        } else {
            console.warn(`Información no encontrada para la sección: ${seccion}`);
        }
    };

    // ==========================================
    // SECCIÓN: LABORATORIOS VIRTUALES
    // ==========================================
    
    /**
     * Nombres amigables de los laboratorios
     */
    const labNames = {
        'programming': 'IDE de Programación',
        'simulator': 'Simulador 3D',
        'ai': 'IA Training Hub',
        'iot': 'IoT Dashboard'
    };
    
    /**
     * Abre un laboratorio virtual específico
     * @param {string} labType - Tipo de laboratorio a abrir
     */
    window.openLab = function(labType) {
        // Verificar si el laboratorio está en mantenimiento
        if (labType === 'ai') {
            alert('El laboratorio de IA está actualmente en mantenimiento. Estará disponible próximamente.');
            return;
        }
        
        // Mostrar mensaje de próximamente para otros laboratorios
        const labName = labNames[labType] || 'Laboratorio';
        alert(`Iniciando ${labName}...\n\nEsta funcionalidad se implementará próximamente.`);
    };

    // ==========================================
    // SECCIÓN: SISTEMA DE NOTIFICACIONES
    // ==========================================
    
    /**
     * Cierra una notificación específica con animación
     * @param {HTMLElement} button - Botón de cerrar que fue presionado
     */
    window.dismissNotification = function(button) {
        const notification = button.closest('.notification-item');
        if (notification) {
            // Aplicar animación de salida
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            
            // Eliminar el elemento del DOM después de la animación
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    };

    /**
     * Auto-dismiss de notificaciones después de un tiempo determinado
     */
    function initializeNotificationAutoDismiss() {
        setTimeout(() => {
            const notifications = document.querySelectorAll('.notification-item');
            notifications.forEach((notification, index) => {
                if (index > 0) { // Mantener la primera notificación visible
                    setTimeout(() => {
                        notification.style.opacity = '0.7';
                    }, index * 5000);
                }
            });
        }, 10000);
    }

    // ==========================================
    // SECCIÓN: ANIMACIONES Y EFECTOS VISUALES
    // ==========================================
    
    /**
     * Configura el observer para animaciones de entrada
     */
    function initializeScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Configurar elementos para animación de entrada
        document.querySelectorAll('.quick-action-card, .stat-card, .crud-action-card, .notification-item').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            observer.observe(el);
        });
    }

    /**
     * Configura efectos hover mejorados para tarjetas de acción rápida
     */
    function initializeHoverEffects() {
        document.querySelectorAll('.quick-action-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                const arrow = this.querySelector('.quick-action-arrow');
                if (arrow) {
                    arrow.style.transform = 'translateX(5px)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                const arrow = this.querySelector('.quick-action-arrow');
                if (arrow) {
                    arrow.style.transform = 'translateX(0)';
                }
            });
        });
    }

    /**
     * Anima los contadores de estadísticas
     */
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/[^0-9]/g, '')) || 0;
            if (target === 0) return;
            
            let current = 0;
            const increment = target / 50;
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    // Mantener formato original si tiene comas o puntos
                    if (counter.textContent.includes(',')) {
                        counter.textContent = Math.floor(current).toLocaleString();
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                    setTimeout(updateCounter, 30);
                } else {
                    // Restaurar valor original completo
                    counter.textContent = target.toLocaleString();
                }
            };
            
            setTimeout(updateCounter, Math.random() * 2000);
        });
    }

    /**
     * Efecto de respiración para elementos robóticos flotantes
     */
    function breatheEffect() {
        const robots = document.querySelectorAll('.floating-robot');
        robots.forEach((robot, index) => {
            robot.style.transform = `translateY(${Math.sin(Date.now() * 0.001 + index) * 10}px)`;
        });
        requestAnimationFrame(breatheEffect);
    }

    // ==========================================
    // SECCIÓN: INICIALIZACIÓN
    // ==========================================
    
    /**
     * Inicializa todos los componentes de la aplicación
     */
    function initializeApp() {
        console.log('Inicializando TECH HOME - Vista Welcome');
        
        // Inicializar componentes principales
        initializeTabs();
        initializeClock();
        
        // Inicializar animaciones y efectos
        initializeScrollAnimations();
        initializeHoverEffects();
        
        // Inicializar notificaciones
        initializeNotificationAutoDismiss();
        
        // Iniciar animaciones con delay
        setTimeout(animateCounters, 1000);
        
        // Iniciar efecto de respiración
        breatheEffect();
        
        console.log('TECH HOME - Vista Welcome inicializada correctamente');
    }

    // Ejecutar inicialización
    initializeApp();
});

// ==========================================
// SECCIÓN: UTILIDADES GLOBALES
// ==========================================

/**
 * Utilidad para logging con timestamp
 * @param {string} message - Mensaje a logear
 * @param {string} level - Nivel de log (info, warn, error)
 */
function techLog(message, level = 'info') {
    const timestamp = new Date().toLocaleTimeString('es-ES');
    const prefix = `[TECH HOME ${timestamp}]`;
    
    switch (level) {
        case 'warn':
            console.warn(prefix, message);
            break;
        case 'error':
            console.error(prefix, message);
            break;
        default:
            console.log(prefix, message);
    }
}

/**
 * Utilidad para formatear números con separadores de miles
 * @param {number} num - Número a formatear
 * @returns {string} Número formateado
 */
function formatNumber(num) {
    return num.toLocaleString('es-ES');
}

/**
 * Utilidad para animar elementos cuando entran en viewport
 * @param {HTMLElement} element - Elemento a animar
 * @param {string} animation - Tipo de animación
 */
function animateOnScroll(element, animation = 'fadeInUp') {
    if (!element) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate', animation);
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    observer.observe(element);
}