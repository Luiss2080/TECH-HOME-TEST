<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Curso extends Model
{
    protected $table = 'cursos';
    protected $primaryKey = 'id';
    
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
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    
    protected $casts = [
        'es_gratuito' => 'boolean',
        'docente_id' => 'integer',
        'categoria_id' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];
    
    // Usar nombres de columna personalizados para timestamps
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    
    public $timestamps = true;

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Categoría a la que pertenece el curso
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Docente que imparte el curso
     */
    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    /**
     * Componentes del curso
     */
    public function componentes(): HasMany
    {
        return $this->hasMany(Componente::class, 'curso_id')->orderBy('orden');
    }

    /**
     * Materiales del curso
     */
    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class, 'curso_id');
    }

    /**
     * Inscripciones al curso
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'curso_id');
    }

    /**
     * Estudiantes inscritos (relación many-to-many a través de inscripciones)
     */
    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'inscripciones', 'curso_id', 'estudiante_id')
                    ->withPivot(['fecha_inscripcion', 'estado', 'progreso'])
                    ->withTimestamps();
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope para cursos publicados
     */
    public function scopePublicados($query)
    {
        return $query->where('estado', 'Publicado');
    }

    /**
     * Scope para cursos por nivel
     */
    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    /**
     * Scope para cursos gratuitos
     */
    public function scopeGratuitos($query)
    {
        return $query->where('es_gratuito', true);
    }

    /**
     * Scope para cursos de pago
     */
    public function scopeDePago($query)
    {
        return $query->where('es_gratuito', false);
    }

    /**
     * Scope para cursos por docente
     */
    public function scopePorDocente($query, $docenteId)
    {
        return $query->where('docente_id', $docenteId);
    }

    /**
     * Scope para cursos por categoría
     */
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    /**
     * Scope para búsqueda por texto
     */
    public function scopeBuscar($query, $texto)
    {
        return $query->where(function($q) use ($texto) {
            $q->where('titulo', 'like', "%{$texto}%")
              ->orWhere('descripcion', 'like', "%{$texto}%");
        });
    }

    // ==========================================
    // MÉTODOS ADICIONALES
    // ==========================================

    /**
     * Obtener URL del video embed de YouTube
     */
    public function getVideoEmbedUrl(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        // Convertir URL de YouTube a formato embed
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches)) {
            return "https://www.youtube.com/embed/{$matches[1]}";
        }

        return null;
    }

    /**
     * Obtener ID del video de YouTube
     */
    public function getYouTubeVideoId(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Obtener thumbnail del video de YouTube
     */
    public function getVideoThumbnail(): ?string
    {
        $videoId = $this->getYouTubeVideoId();
        if (!$videoId) {
            return $this->imagen_portada;
        }

        return "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
    }

    /**
     * Verificar si un usuario está inscrito en el curso
     */
    public function hasStudent($userId): bool
    {
        return $this->inscripciones()
                    ->where('estudiante_id', $userId)
                    ->where('estado', 'activo')
                    ->exists();
    }

    /**
     * Obtener número total de estudiantes inscritos
     */
    public function getTotalStudents(): int
    {
        return $this->inscripciones()
                    ->where('estado', 'activo')
                    ->count();
    }

    /**
     * Obtener progreso promedio de los estudiantes
     */
    public function getAverageProgress(): float
    {
        return $this->inscripciones()
                    ->where('estado', 'activo')
                    ->avg('progreso') ?: 0;
    }

    /**
     * Obtener duración total estimada del curso (en minutos)
     */
    public function getDurationMinutes(): int
    {
        return $this->componentes()
                    ->where('estado', 'Publicado')
                    ->sum('duracion_estimada') ?: 0;
    }

    /**
     * Obtener estadísticas del curso
     */
    public function getStats(): array
    {
        return [
            'total_estudiantes' => $this->getTotalStudents(),
            'progreso_promedio' => $this->getAverageProgress(),
            'total_componentes' => $this->componentes()->where('estado', 'Publicado')->count(),
            'total_materiales' => $this->materiales()->where('estado', 1)->count(),
            'duracion_estimada' => $this->getDurationMinutes()
        ];
    }

    /**
     * Formatear nivel para mostrar
     */
    public function getNivelFormatted(): string
    {
        return match($this->nivel) {
            'Principiante' => '🟢 Principiante',
            'Intermedio' => '🟡 Intermedio',
            'Avanzado' => '🔴 Avanzado',
            default => $this->nivel
        };
    }

    /**
     * Obtener clase CSS para el nivel
     */
    public function getNivelClass(): string
    {
        return match($this->nivel) {
            'Principiante' => 'badge-success',
            'Intermedio' => 'badge-warning',
            'Avanzado' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Scope para ordenar por popularidad (más estudiantes)
     */
    public function scopePopular($query)
    {
        return $query->withCount(['inscripciones' => function($q) {
            $q->where('estado', 'activo');
        }])->orderBy('inscripciones_count', 'desc');
    }

    /**
     * Scope para cursos recientes
     */
    public function scopeRecientes($query, $days = 30)
    {
        return $query->where('fecha_creacion', '>=', now()->subDays($days));
    }
}