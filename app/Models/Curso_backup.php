<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Exception;

class Curso extends Model
{
    protected $table = 'courses';
    
    protected $fillable = [
        'titulo',
        'descripcion',
        'video_url',
        'docente_id',
        'categoria_id',
        'imagen_portada',
        'nivel',
        'estado',
        'es_gratuito',
        'precio',
        'duracion'
    ];
    
    protected $casts = [
        'es_gratuito' => 'boolean',
        'precio' => 'decimal:2'
    ];
    
    // Usar timestamps estándar de Laravel
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // ==========================================
    // RELACIONES ELOQUENT
    // ==========================================

    /**
     * Obtener el docente que imparte el curso
     */
    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    /**
     * Obtener la categoría del curso
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Obtener los materiales del curso
     */
    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Obtener las inscripciones del curso
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Obtener los estudiantes inscritos
     */
    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'curso_id', 'user_id')
                    ->withTimestamps();
    }

    // ==========================================
    // SCOPES ESTÁTICOS
    // ==========================================

    /**
     * Obtener cursos publicados
     */
    public static function publicados()
    {
        return self::where('estado', '=', 'Publicado');
    }

    /**
     * Obtener cursos por nivel
     */
    public static function porNivel(string $nivel)
    {
        return self::where('nivel', '=', $nivel);
    }

    /**
     * Obtener cursos por docente
     */
    public static function porDocente(int $docenteId)
    {
        return self::where('docente_id', '=', $docenteId);
    }

    /**
     * Obtener cursos por categoría
     */
    public static function porCategoria(int $categoriaId)
    {
        return self::where('categoria_id', '=', $categoriaId);
    }

    // ==========================================
    // MÉTODOS DE UTILIDAD
    // ==========================================

    /**
     * Obtener clase CSS según el estado
     */
    public function getEstadoClass(): string
    {
        $classes = [
            'Publicado' => 'success',
            'Borrador' => 'secondary',
            'Archivado' => 'warning'
        ];
        
        return $classes[$this->estado] ?? 'secondary';
    }

    /**
     * Obtener clase CSS según el nivel
     */
    public function getNivelClass(): string
    {
        $classes = [
            'Principiante' => 'success',
            'Intermedio' => 'warning',
            'Avanzado' => 'danger'
        ];
        
        return $classes[$this->nivel] ?? 'secondary';
    }

    /**
     * Verificar si el curso está disponible para visualización
     */
    public function estaDisponible(): bool
    {
        return $this->estado === 'Publicado' && !empty($this->video_url);
    }

    /**
     * Obtener URL de la imagen de portada
     */
    public function getImagenPortadaUrl(): string
    {
        if (!$this->imagen_portada) {
            return asset('images/cursos/default.jpg');
        }
        
        return asset('images/cursos/' . $this->imagen_portada);
    }

    /**
     * Obtener ID del video de YouTube
     */
    public function getYoutubeVideoId(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        // Extraer ID de diferentes formatos de URL de YouTube
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->video_url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Obtener URL de embed de YouTube
     */
    public function getYoutubeEmbedUrl(): ?string
    {
        $videoId = $this->getYoutubeVideoId();
        if (!$videoId) {
            return null;
        }

        return "https://www.youtube.com/embed/{$videoId}";
    }

    /**
     * Obtener thumbnail de YouTube
     */
    public function getYoutubeThumbnail(): ?string
    {
        $videoId = $this->getYoutubeVideoId();
        if (!$videoId) {
            return null;
        }

        return "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
    }

    /**
     * Verificar si la URL es de YouTube válida
     */
    public function tieneVideoYoutube(): bool
    {
        return !is_null($this->getYoutubeVideoId());
    }

    /**
     * Obtener información completa del curso (simplificada)
     */
    public function getInformacionCompleta(): array
    {
        $docente = $this->docente();
        $categoria = $this->categoria();
        
        return [
            'basica' => $this->getAttributes(),
            'docente' => $docente ? [
                'nombre' => $docente->nombre,
                'apellido' => $docente->apellido,
                'email' => $docente->email
            ] : null,
            'categoria' => $categoria ? [
                'nombre' => $categoria->nombre,
                'color' => $categoria->color ?? '#007bff',
                'icono' => $categoria->icono ?? 'play-circle'
            ] : null,
            'video' => [
                'url' => $this->video_url,
                'embed_url' => $this->getYoutubeEmbedUrl(),
                'thumbnail' => $this->getYoutubeThumbnail(),
                'video_id' => $this->getYoutubeVideoId(),
                'es_youtube' => $this->tieneVideoYoutube()
            ]
        ];
    }
}
