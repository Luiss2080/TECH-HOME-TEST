{{-- ============================================ --}}
{{-- FOOTER TECH HOME - Instituto de Robótica --}}
{{-- ============================================ --}}

<footer class="tech-footer">
    <!-- Fondo animado -->
    <div class="footer-background">
        <div class="footer-gradient"></div>
        <div class="footer-particles"></div>
    </div>

    <!-- Contenido principal del footer -->
    <div class="footer-content">
        <div class="footer-container">
            <!-- Sección de Contacto -->
            <div class="footer-section contact-section">
                <h3 class="section-title">
                    <i class="fas fa-address-book"></i>
                    Contacto
                </h3>
                <div class="section-divider"></div>

                <div class="contact-items">
                    <!-- Dirección -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info">
                            <span class="info-label">Dirección:</span>
                            <span class="info-text">Av. Tecnológica #456</span>
                            <span class="info-subtext">Santa Cruz, Bolivia</span>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-info">
                            <span class="info-label">Teléfono:</span>
                            <a href="tel:+59137890123" class="info-link">
                                +591 3 789-0123
                            </a>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info">
                            <span class="info-label">Email:</span>
                            <a href="mailto:info@techhome.edu.bo" class="info-link">
                                info@techhome.edu.bo
                            </a>
                        </div>
                    </div>

                    <!-- Horarios -->
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-info">
                            <span class="info-label">Horarios:</span>
                            <span class="info-text">Lun - Vie: 7:00 - 19:00</span>
                            <span class="info-subtext">Sáb: 8:00 - 12:00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección TECH HOME -->
            <div class="footer-section company-section">
                <h3 class="section-title">
                    <i class="fas fa-graduation-cap"></i>
                    TECH HOME
                </h3>
                <div class="section-divider"></div>

                <div class="company-content">
                    <div class="company-description">
                        <p>Instituto de excelencia en robótica y tecnología. Formamos profesionales capacitados para liderar la revolución tecnológica del futuro.</p>
                    </div>

                    <!-- Características distintivas -->
                    <div class="features-list">
                        <div class="feature-item">
                            <i class="fas fa-certificate"></i>
                            <span>Certificado</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-lightbulb"></i>
                            <span>Innovador</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-universal-access"></i>
                            <span>Accesible</span>
                        </div>
                    </div>

                    <!-- Información de la versión -->
                    <div class="version-info">
                        <div class="version-badge">
                            <span class="version-text">Versión 2.0</span>
                        </div>
                        <div class="build-info">
                            <span>Build #2025.1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de redes sociales y copyright -->
    <div class="footer-bottom">
        <div class="footer-container">
            <!-- Logo y nombre -->
            <div class="footer-brand">
                <div class="brand-logo">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="brand-text">
                    <h4>TECH HOME</h4>
                    <span>Instituto de Robótica y Tecnología</span>
                </div>
            </div>

            <!-- Redes sociales -->
            <div class="social-networks">
                <a href="https://facebook.com/techhome" 
                   target="_blank" 
                   class="social-link facebook"
                   title="Síguenos en Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                
                <a href="https://instagram.com/techhome" 
                   target="_blank" 
                   class="social-link instagram"
                   title="Síguenos en Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                
                <a href="https://twitter.com/techhome" 
                   target="_blank" 
                   class="social-link twitter"
                   title="Síguenos en Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                
                <a href="https://wa.me/59137890123" 
                   target="_blank" 
                   class="social-link whatsapp"
                   title="Contáctanos por WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                
                <a href="https://linkedin.com/company/techhome" 
                   target="_blank" 
                   class="social-link linkedin"
                   title="Conéctate en LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                
                <a href="https://youtube.com/@techhome" 
                   target="_blank" 
                   class="social-link youtube"
                   title="Suscríbete a nuestro canal">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>

            <!-- Copyright y enlaces legales -->
            <div class="copyright-section">
                <div class="copyright-text">
                    <p>&copy; {{ date('Y') }} Instituto Tech Home. Todos los derechos reservados.</p>
                </div>
                <div class="legal-links">
                    <a href="#" class="legal-link">
                        Política de Privacidad
                    </a>
                    <span class="separator">|</span>
                    <a href="#" class="legal-link">
                        Términos de Servicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicador de scroll to top -->
    <div class="scroll-to-top" id="scrollToTop">
        <i class="fas fa-chevron-up"></i>
        <span class="tooltip">Volver arriba</span>
    </div>
</footer>

{{-- Scripts específicos del footer --}}
@push('scripts')
<script>
    // Datos de contacto dinámicos
    window.footerData = {
        phone: '+591 3 789-0123',
        email: 'info@techhome.edu.bo',
        address: 'Av. Tecnológica #456, Santa Cruz - Bolivia',
        socialMedia: {
            facebook: 'https://facebook.com/techhome',
            instagram: 'https://instagram.com/techhome',
            twitter: 'https://twitter.com/techhome',
            whatsapp: 'https://wa.me/59137890123',
            linkedin: 'https://linkedin.com/company/techhome',
            youtube: 'https://youtube.com/@techhome'
        }
    };
</script>
@endpush