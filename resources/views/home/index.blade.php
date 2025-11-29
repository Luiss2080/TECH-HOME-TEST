@extends('layouts.app')

@section('title', 'TECH HOME - Instituto de Robótica y Tecnología')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home/index.css') }}">
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="tech-hero">
        <div class="hero-background">
            <div class="circuit-pattern"></div>
            <div class="floating-particles"></div>
        </div>
        
        <div class="hero-content">
            <div class="hero-main">
                <div class="hero-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <h1 class="hero-title">
                    Instituto de Robótica
                    <span class="hero-brand">TECH HOME</span>
                </h1>
                <p class="hero-subtitle">
                    @auth
                        Hola {{ Auth::user()->nombre }}, bienvenido al futuro de la robótica y la tecnología
                    @else
                        Portal de acceso al ecosistema tecnológico más avanzado
                    @endauth
                </p>
                
                @guest
                <div class="hero-actions">
                    <a href="{{ route('auth.register') }}" class="btn btn-primary hero-btn">
                        <i class="fas fa-rocket"></i>
                        Comenzar Ahora
                    </a>
                    <a href="{{ route('auth.login') }}" class="btn btn-secondary hero-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Iniciar Sesión
                    </a>
                </div>
                @endguest
            </div>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $estadisticas['total_cursos'] ?? 0 }}</div>
                    <div class="stat-label">Cursos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $estadisticas['total_estudiantes'] ?? 0 }}</div>
                    <div class="stat-label">Estudiantes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $estadisticas['total_docentes'] ?? 0 }}</div>
                    <div class="stat-label">Docentes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $estadisticas['total_libros'] ?? 0 }}</div>
                    <div class="stat-label">Recursos</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cursos Destacados -->
    @if(isset($cursosDestacados) && $cursosDestacados->count() > 0)
    <section class="courses-section">
        <div class="container">
            <div class="section-header">
                <div class="section-title-group">
                    <h2 class="section-title">
                        <i class="fas fa-graduation-cap"></i>
                        Cursos Destacados
                    </h2>
                    <p class="section-subtitle">Descubre nuestros cursos más populares y avanzados</p>
                </div>
                <a href="/cursos" class="view-all-btn">
                    Ver todos <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="courses-grid">
                @foreach($cursosDestacados as $curso)
                <div class="course-card">
                    <div class="course-image">
                        @if($curso->imagen)
                            <img src="{{ asset('storage/cursos/' . $curso->imagen) }}" alt="{{ $curso->titulo }}">
                        @else
                            <div class="course-placeholder">
                                <i class="fas fa-robot"></i>
                            </div>
                        @endif
                        <div class="course-badge">{{ $curso->categoria->nombre ?? 'General' }}</div>
                    </div>
                    
                    <div class="course-content">
                        <h3 class="course-title">{{ $curso->titulo }}</h3>
                        <p class="course-description">{{ Str::limit($curso->descripcion, 120) }}</p>
                        
                        <div class="course-meta">
                            <div class="course-instructor">
                                <i class="fas fa-user"></i>
                                {{ $curso->docente->nombre ?? 'TECH HOME' }}
                            </div>
                            <div class="course-duration">
                                <i class="fas fa-clock"></i>
                                {{ $curso->duracion ?? '8 semanas' }}
                            </div>
                        </div>
                        
                        <div class="course-footer">
                            <div class="course-price">
                                @if($curso->precio && $curso->precio > 0)
                                    <span class="price">${{ number_format($curso->precio, 0) }}</span>
                                @else
                                    <span class="free">Gratuito</span>
                                @endif
                            </div>
                            <a href="/cursos/{{ $curso->id }}" class="btn btn-primary btn-sm">
                                Ver Curso
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Recursos y Libros -->
    @if(isset($librosRecientes) && $librosRecientes->count() > 0)
    <section class="resources-section">
        <div class="container">
            <div class="section-header">
                <div class="section-title-group">
                    <h2 class="section-title">
                        <i class="fas fa-book"></i>
                        Recursos Digitales
                    </h2>
                    <p class="section-subtitle">Biblioteca digital con los mejores recursos de tecnología</p>
                </div>
                <a href="/libros" class="view-all-btn">
                    Ver biblioteca <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="resources-grid">
                @foreach($librosRecientes as $libro)
                <div class="resource-card">
                    <div class="resource-icon">
                        @if($libro->tipo === 'pdf')
                            <i class="fas fa-file-pdf"></i>
                        @elseif($libro->tipo === 'video')
                            <i class="fas fa-play-circle"></i>
                        @else
                            <i class="fas fa-book"></i>
                        @endif
                    </div>
                    
                    <div class="resource-content">
                        <div class="resource-category">{{ $libro->categoria->nombre ?? 'General' }}</div>
                        <h4 class="resource-title">{{ $libro->titulo }}</h4>
                        <p class="resource-description">{{ Str::limit($libro->descripcion, 100) }}</p>
                        
                        <div class="resource-meta">
                            <span class="resource-type">{{ strtoupper($libro->tipo ?? 'LIBRO') }}</span>
                            <span class="resource-date">{{ $libro->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="resource-actions">
                        <a href="/libros/{{ $libro->id }}" class="btn btn-outline">
                            <i class="fas fa-eye"></i>
                            Ver
                        </a>
                        @auth
                        <a href="/libros/{{ $libro->id }}/download" class="btn btn-primary">
                            <i class="fas fa-download"></i>
                            Descargar
                        </a>
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Categorías -->
    @if(isset($categorias) && $categorias->count() > 0)
    <section class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-th-large"></i>
                    Áreas de Especialización
                </h2>
                <p class="section-subtitle">Explora nuestras diferentes áreas de conocimiento tecnológico</p>
            </div>
            
            <div class="categories-grid">
                @foreach($categorias as $categoria)
                <a href="/categoria/{{ $categoria->id }}" class="category-card">
                    <div class="category-icon">
                        @switch($categoria->nombre)
                            @case('Robótica')
                                <i class="fas fa-robot"></i>
                                @break
                            @case('Inteligencia Artificial')
                                <i class="fas fa-brain"></i>
                                @break
                            @case('Programación')
                                <i class="fas fa-code"></i>
                                @break
                            @case('Electrónica')
                                <i class="fas fa-microchip"></i>
                                @break
                            @default
                                <i class="fas fa-cog"></i>
                        @endswitch
                    </div>
                    
                    <div class="category-content">
                        <h3 class="category-title">{{ $categoria->nombre }}</h3>
                        <div class="category-stats">
                            <span>{{ $categoria->cursos_count ?? 0 }} cursos</span>
                            <span>{{ $categoria->libros_count ?? 0 }} recursos</span>
                        </div>
                    </div>
                    
                    <div class="category-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Call to Action -->
    @guest
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <div class="cta-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h2 class="cta-title">¿Listo para comenzar tu viaje tecnológico?</h2>
                <p class="cta-subtitle">Únete a miles de estudiantes que ya están construyendo el futuro</p>
                <div class="cta-actions">
                    <a href="{{ route('auth.register') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus"></i>
                        Registrarse Gratis
                    </a>
                    <a href="/about" class="btn btn-outline btn-lg">
                        <i class="fas fa-info-circle"></i>
                        Conocer Más
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endguest

    <!-- Newsletter -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h3>Mantente actualizado</h3>
                    <p>Recibe las últimas noticias y recursos directamente en tu correo</p>
                </div>
                <div class="newsletter-form">
                    <form id="newsletter-form" action="/newsletter/subscribe" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="email" placeholder="Tu correo electrónico" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Suscribirse
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/home/index.js') }}"></script>
@endpush