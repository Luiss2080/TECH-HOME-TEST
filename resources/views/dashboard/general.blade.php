@extends('layouts.app')

@section('title', 'Dashboard - TECH HOME')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/general.css') }}">
@endpush

@section('content')
    <div class="dashboard-container">
        <!-- Welcome Header -->
        <div class="welcome-header">
            <div class="welcome-content">
                <div class="welcome-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="welcome-text">
                    <h1>¡Bienvenido, {{ $user->nombre }}!</h1>
                    <p>Estás listo para explorar el mundo de la tecnología y la robótica</p>
                </div>
            </div>
            <div class="welcome-status">
                <div class="status-indicator online">
                    <i class="fas fa-circle"></i>
                    En línea
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2 class="section-title">
                <i class="fas fa-zap"></i>
                Acciones Rápidas
            </h2>
            
            <div class="actions-grid">
                <a href="/cursos" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Explorar Cursos</h3>
                    <p>Descubre nuestros cursos de tecnología</p>
                    <div class="action-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="/libros" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3>Biblioteca Digital</h3>
                    <p>Accede a recursos y documentos</p>
                    <div class="action-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="/laboratorios" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h3>Laboratorios</h3>
                    <p>Practica en entornos virtuales</p>
                    <div class="action-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="/perfil" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h3>Mi Perfil</h3>
                    <p>Configura tu cuenta y preferencias</p>
                    <div class="action-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h2 class="section-title">
                <i class="fas fa-history"></i>
                Actividad Reciente
            </h2>
            
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-icon new">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-content">
                        <h4>¡Bienvenido a TECH HOME!</h4>
                        <p>Tu cuenta ha sido creada exitosamente. Comienza explorando nuestros cursos.</p>
                        <span class="activity-time">Hace unos momentos</span>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon info">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="activity-content">
                        <h4>Configura tu perfil</h4>
                        <p>Completa tu perfil para obtener recomendaciones personalizadas.</p>
                        <span class="activity-time">Pendiente</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="system-status">
            <h2 class="section-title">
                <i class="fas fa-server"></i>
                Estado del Sistema
            </h2>
            
            <div class="status-grid">
                <div class="status-card">
                    <div class="status-icon online">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="status-info">
                        <h4>Base de Datos</h4>
                        <p class="status-text online">Operativo</p>
                    </div>
                </div>

                <div class="status-card">
                    <div class="status-icon online">
                        <i class="fas fa-cloud"></i>
                    </div>
                    <div class="status-info">
                        <h4>Servicios Cloud</h4>
                        <p class="status-text online">Conectado</p>
                    </div>
                </div>

                <div class="status-card">
                    <div class="status-icon online">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="status-info">
                        <h4>Seguridad</h4>
                        <p class="status-text online">Protegido</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="help-section">
            <div class="help-card">
                <div class="help-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div class="help-content">
                    <h3>¿Necesitas ayuda?</h3>
                    <p>Nuestro equipo está aquí para apoyarte en tu viaje tecnológico</p>
                    <a href="/contact" class="btn btn-outline">
                        <i class="fas fa-headset"></i>
                        Contactar Soporte
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/general.js') }}"></script>
@endpush