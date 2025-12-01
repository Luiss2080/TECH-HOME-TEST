    // Función para cambiar tabs de información
    document.querySelectorAll('.crud-info-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.crud-info-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.crud-info-pane').forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');
            const targetPane = document.getElementById(this.dataset.tab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // Actualizar reloj en tiempo real
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
    
    updateClock();
    setInterval(updateClock, 1000);

    // Navegación a diferentes secciones (para vista generalizada)
    window.showInfo = function(seccion) {
        const infoSecciones = {
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
                titulo: 'Inteligencia Artificial - Biblioteca Digital',
                descripcion: 'Recursos completos sobre Machine Learning, Deep Learning y Redes Neuronales.',
                caracteristicas: [
                    '• 632 títulos sobre IA y aprendizaje automático',
                    '• Tutoriales prácticos con Python y TensorFlow',
                    '• Teoría fundamental y matemáticas para IA',
                    '• Aplicaciones en visión artificial y PLN'
                ]
            },
            'programacion-libros': {
                titulo: 'Programación Avanzada - Biblioteca Digital',
                descripcion: 'Libros sobre lenguajes de programación para sistemas embebidos y robótica.',
                caracteristicas: [
                    '• C++, Python, ROS y MATLAB',
                    '• Programación de microcontroladores',
                    '• Algoritmos y estructuras de datos',
                    '• Desarrollo de software en tiempo real'
                ]
            },
            'control-libros': {
                titulo: 'Ingeniería de Control - Biblioteca Digital',
                descripcion: 'Fundamentos y aplicaciones de sistemas de control automático.',
                caracteristicas: [
                    '• Control PID, adaptativo y robusto',
                    '• Modelado matemático de sistemas dinámicos',
                    '• Análisis de estabilidad y respuesta en frecuencia',
                    '• Control de robots manipuladores'
                ]
            },
            
            // Centro de Componentes
            'microcontroladores': {
                titulo: 'Microcontroladores - Centro de Componentes',
                descripcion: 'Plataformas de hardware para el cerebro de tus robots.',
                caracteristicas: [
                    '• Arduino (Uno, Mega, Nano, Due)',
                    '• Raspberry Pi (4, 3B+, Zero W)',
                    '• ESP32 y ESP8266 para IoT',
                    '• STM32 y otros microcontroladores ARM'
                ]
            },
            'sensores': {
                titulo: 'Sensores Avanzados - Centro de Componentes',
                descripcion: 'Dispositivos para que tus robots perciban el entorno.',
                caracteristicas: [
                    '• LIDAR y sensores de distancia láser',
                    '• Cámaras y módulos de visión artificial',
                    '• IMUs (Acelerómetros y Giroscopios)',
                    '• Sensores ambientales y biométricos'
                ]
            },
            'actuadores': {
                titulo: 'Actuadores y Motores - Centro de Componentes',
                descripcion: 'Sistemas de movimiento y actuación para mecanismos robóticos.',
                caracteristicas: [
                    '• Servomotores digitales y analógicos',
                    '• Motores paso a paso NEMA',
                    '• Motores DC con encoder',
                    '• Actuadores lineales y neumáticos'
                ]
            },
            'alimentacion': {
                titulo: 'Fuentes y Baterías - Centro de Componentes',
                descripcion: 'Energía confiable para tus proyectos autónomos.',
                caracteristicas: [
                    '• Baterías LiPo de alta descarga',
                    '• Celdas 18650 y gestores de carga (BMS)',
                    '• Convertidores DC-DC (Buck/Boost)',
                    '• Fuentes de alimentación regulables'
                ]
            },

            // Cursos
            'robotica-basica': {
                titulo: 'Curso de Robótica Básica',
                descripcion: 'Inicia tu camino en el mundo de la robótica desde cero.',
                caracteristicas: [
                    '• Fundamentos de electrónica y mecánica',
                    '• Programación básica con Arduino',
                    '• Construcción de tu primer robot móvil',
                    '• Certificado de finalización incluido'
                ]
            },
            'machine-learning': {
                titulo: 'Curso de Machine Learning',
                descripcion: 'Domina los algoritmos que impulsan la inteligencia artificial moderna.',
                caracteristicas: [
                    '• Aprendizaje supervisado y no supervisado',
                    '• Redes neuronales y Deep Learning',
                    '• Proyectos prácticos con datos reales',
                    '• Implementación en sistemas robóticos'
                ]
            },
            'vision-artificial': {
                titulo: 'Curso de Visión Artificial',
                descripcion: 'Enseña a las máquinas a ver y entender el mundo visual.',
                caracteristicas: [
                    '• Procesamiento de imágenes con OpenCV',
                    '• Detección y reconocimiento de objetos',
                    '• Seguimiento visual y navegación',
                    '• Integración con cámaras robóticas'
                ]
            },
            'iot-automatizacion': {
                titulo: 'Curso de IoT y Automatización',
                descripcion: 'Conecta tus dispositivos y crea sistemas inteligentes.',
                caracteristicas: [
                    '• Protocolos de comunicación (MQTT, HTTP)',
                    '• Plataformas en la nube para IoT',
                    '• Domótica y control remoto',
                    '• Seguridad en dispositivos conectados'
                ]
            },

            // Acciones generales
            'biblioteca': {
                titulo: 'Biblioteca Digital TECH HOME',
                descripcion: 'Acceso ilimitado a miles de recursos educativos.',
                caracteristicas: [
                    '• Libros electrónicos y papers',
                    '• Revistas científicas y tesis',
                    '• Material multimedia y tutoriales',
                    '• Acceso 24/7 desde cualquier dispositivo'
                ]
            },
            'componentes': {
                titulo: 'Centro de Componentes TECH HOME',
                descripcion: 'Todo el hardware que necesitas en un solo lugar.',
                caracteristicas: [
                    '• Catálogo actualizado en tiempo real',
                    '• Envíos a todo el país',
                    '• Garantía y soporte técnico',
                    '• Descuentos para estudiantes'
                ]
            },
            'cursos': {
                titulo: 'Academia TECH HOME',
                descripcion: 'Formación de excelencia en tecnología y robótica.',
                caracteristicas: [
                    '• Instructores expertos en la industria',
                    '• Metodología práctica y proyectos reales',
                    '• Comunidad de aprendizaje activa',
                    '• Bolsa de trabajo para graduados'
                ]
            },
            'proyectos': {
                titulo: 'Laboratorio de Proyectos',
                descripcion: 'Innovación y desarrollo tecnológico en marcha.',
                caracteristicas: [
                    '• Proyectos de investigación aplicada',
                    '• Colaboración con empresas y universidades',
                    '• Oportunidades de pasantías',
                    '• Exhibición de prototipos'
                ]
            }
        };

        const info = infoSecciones[seccion];
        
        if (info) {
            let caracteristicasHtml = '';
            if (info.caracteristicas) {
                caracteristicasHtml = '<ul style="text-align: left; margin-top: 10px; color: #4b5563;">' + 
                    info.caracteristicas.map(c => `<li>${c}</li>`).join('') + 
                    '</ul>';
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: info.titulo,
                    html: `<p>${info.descripcion}</p>${caracteristicasHtml}`,
                    icon: 'info',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#dc2626',
                    width: '600px'
                });
            } else {
                alert(`${info.titulo}\n\n${info.descripcion}`);
            }
        } else {
            console.log('Sección no encontrada:', seccion);
        }
    };

    window.openLab = function(labType) {
        const labs = {
            'programming': 'IDE de Programación',
            'simulator': 'Simulador 3D',
            'ai': 'IA Training Hub',
            'iot': 'IoT Dashboard'
        };

        const labName = labs[labType] || 'Laboratorio Virtual';

        if (typeof Swal !== 'undefined') {
            let icon = 'success';
            let title = `Iniciando ${labName}`;
            let text = 'Preparando entorno virtual...';

            if (labType === 'ai') {
                icon = 'warning';
                title = 'Mantenimiento';
                text = 'El Hub de IA está en mantenimiento programado.';
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(`Abriendo ${labName}...`);
        }
    };

    window.dismissNotification = function(btn) {
        const notification = btn.closest('.notification-item');
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 300);
    };
});
</script>
@endpush