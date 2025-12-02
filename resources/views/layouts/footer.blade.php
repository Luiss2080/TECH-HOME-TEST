<!-- ============================================================================
 FOOTER TECH HOME - Instituto de Robótica
 ============================================================================ -->
<div class="footer-container-wrapper">
    <footer class="tech-home-footer">
    <div class="footer-container">
        <!-- ============================================================================
         CONTENIDO PRINCIPAL DEL FOOTER
         ============================================================================ -->
        <div class="footer-main-content {{ auth()->check() ? 'authenticated' : 'non-authenticated' }}">
            <!-- Columna 1: Información de Contacto -->
            <div class="footer-column footer-contact-column">
                <h6 class="footer-column-title">Contacto</h6>
                <div class="footer-contact-info">
                    <!-- Dirección física -->
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Av. Tecnológica #456<br>Santa Cruz, Bolivia</span>
                    </div>
                    <!-- Número de teléfono -->
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+591 3 789-0123</span>
                    </div>
                    <!-- Correo electrónico -->
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>info@techhome.edu.bo</span>
                    </div>
                    <!-- Horarios de atención -->
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span>Lun - Vie: 7:00 - 19:00<br>Sáb: 8:00 - 12:00</span>
                    </div>
                </div>
            </div>

            @auth
                <!-- Columna 2: Enlaces de Navegación del Sistema -->
                <div class="footer-column footer-nav-column">
                    <h6 class="footer-column-title">Sistema</h6>
                    <div class="footer-nav-links">
                        <!-- Enlace al Dashboard principal -->
                        <a href="{{ route(Dashboard()) }}" class="footer-nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                        <!-- Gestión de Estudiantes -->
                        <a href="{{ route('estudiantes') }}" class="footer-nav-link">
                            <i class="fas fa-user-graduate"></i>
                            Estudiantes
                        </a>
                        <!-- Catálogo de Cursos -->
                        <a href="{{ route('cursos') }}" class="footer-nav-link">
                            <i class="fas fa-graduation-cap"></i>
                            Cursos
                        </a>
                        <!-- Biblioteca Digital -->
                        <a href="{{ route('libros') }}" class="footer-nav-link">
                            <i class="fas fa-book"></i>
                            Biblioteca
                        </a>
                        <!-- Materiales Educativos -->
                        <a href="{{ route('materiales') }}" class="footer-nav-link">
                            <i class="fas fa-file-alt"></i>
                            Materiales
                        </a>
                        <!-- Administración de Usuarios -->
                        <a href="{{ route('usuarios') }}" class="footer-nav-link">
                            <i class="fas fa-users-cog"></i>
                            Usuarios
                        </a>
                    </div>
                </div>
            @endauth

            <!-- Columna 3: Información Acerca del Instituto -->
            <div class="footer-column footer-about-column">
                <h6 class="footer-column-title">TECH HOME</h6>
                <div class="footer-about">
                    <!-- Descripción del instituto -->
                    <p class="footer-about-text">
                        Instituto de excelencia en robótica y tecnología.
                        Formamos profesionales capacitados para liderar
                        la revolución tecnológica del futuro.
                    </p>
                    <!-- Características destacadas -->
                    <div class="footer-features">
                        <div class="feature-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Certificado</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-rocket"></i>
                            <span>Innovador</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Accesible</span>
                        </div>
                    </div>
                    <!-- Información de versión -->
                    <div class="footer-version">
                        <span class="version-badge">Versión 2.0</span>
                        <span class="build-info">Build #2025.1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Separador principal horizontal -->
        <div class="footer-main-divider"></div>

        <!-- ============================================================================
         FOOTER INFERIOR
         ============================================================================ -->
        <div class="footer-bottom">
            <!-- Sección izquierda: Marca y logo -->
            <div class="footer-bottom-left">
                <div class="footer-brand-section">
                    <!-- Icono de la marca -->
                    <div class="footer-logo-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <!-- Texto de la marca -->
                    <div class="footer-brand-text">
                        <h5 class="footer-brand-name">TECH HOME</h5>
                        <p class="footer-brand-tagline">Instituto de Robótica y Tecnología</p>
                    </div>
                </div>
            </div>

            <!-- Sección central: Redes sociales -->
            <div class="footer-bottom-center">
                <div class="footer-social-links">
                    <!-- Enlaces a redes sociales -->
                    <a href="#" class="social-link facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="#" class="social-link linkedin">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link youtube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Sección derecha: Información legal -->
            <div class="footer-bottom-right">
                <div class="footer-legal">
                    <!-- Copyright con año dinámico -->
                    <p class="copyright-text">
                        &copy; {{ date('Y') }} Instituto Tech Home. Todos los derechos reservados.
                    </p>
                    <!-- Enlaces legales -->
                    <div class="legal-links">
                        <a href="#" class="legal-link">Política de Privacidad</a>
                        <span class="legal-separator">|</span>
                        <a href="#" class="legal-link">Términos de Servicio</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</div>