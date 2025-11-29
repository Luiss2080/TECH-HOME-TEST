<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'es_gratuito'
    ];
    
    protected $casts = [
        'es_gratuito' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];
    
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Categoría del curso
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
        return $this->hasMany(Enrollment::class, 'curso_id');
    }

    /**
     * Estudiantes inscritos
     */
    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'curso_id', 'estudiante_id')
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
     * Scope para filtrar por tipo de curso
     */
    public function scopeTipoCurso($query, $esGratuito = null)
    {
        if ($esGratuito !== null) {
            return $query->where('es_gratuito', $esGratuito);
        }
        return $query;
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
     * Scope para búsqueda por texto (optimizado)
     */
    public function scopeBuscar($query, $texto)
    {
        if (empty($texto)) return $query;
        
        $texto = trim($texto);
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
        if (empty($this->video_url)) {
            return null;
        }

        // Extraer ID del video de YouTube
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return null;
    }

    /**
     * Verificar si es un video válido de YouTube
     */
    public function isValidYoutubeUrl(): bool
    {
        if (empty($this->video_url)) {
            return false;
        }

        return preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//', $this->video_url) === 1;
    }

    /**
     * Obtener conteo de estudiantes inscritos
     */
    public function getEstudiantesCountAttribute(): int
    {
        return $this->inscripciones()->count();
    }
}