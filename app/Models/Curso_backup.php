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
        'es_gratuito',
        'precio',
        'duracion'
    ];
    
    protected $casts = [
        'es_gratuito' => 'boolean',
        'precio' => 'decimal:2'
    ];

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
    // SCOPES
    // ==========================================

    /**
     * Scope para cursos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para cursos publicados
     */
    public function scopePublicados($query)
    {
        return $query->where('estado', 'publicado');
    }

    /**
     * Scope para cursos gratuitos
     */
    public function scopeGratuitos($query)
    {
        return $query->where('es_gratuito', true);
    }

    /**
     * Scope para cursos por nivel
     */
    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    /**
     * Scope para cursos por categoría
     */
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    /**
     * Obtener la URL completa de la imagen
     */
    public function getImagenCompletaAttribute()
    {
        if ($this->imagen_portada) {
            return asset('storage/cursos/' . $this->imagen_portada);
        }
        return asset('images/default-course.jpg');
    }

    /**
     * Obtener el precio formateado
     */
    public function getPrecioFormateadoAttribute()
    {
        if ($this->es_gratuito) {
            return 'Gratuito';
        }
        return '$' . number_format($this->precio, 0);
    }

    /**
     * Obtener el nivel formateado
     */
    public function getNivelFormateadoAttribute()
    {
        $niveles = [
            'principiante' => 'Principiante',
            'intermedio' => 'Intermedio',
            'avanzado' => 'Avanzado'
        ];

        return $niveles[$this->nivel] ?? ucfirst($this->nivel);
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS
    // ==========================================

    /**
     * Obtener cursos populares
     */
    public static function populares($limite = 6)
    {
        return static::activos()
                    ->withCount('inscripciones')
                    ->orderBy('inscripciones_count', 'desc')
                    ->limit($limite)
                    ->get();
    }

    /**
     * Obtener cursos recientes
     */
    public static function recientes($limite = 6)
    {
        return static::activos()
                    ->orderBy('created_at', 'desc')
                    ->limit($limite)
                    ->get();
    }
}