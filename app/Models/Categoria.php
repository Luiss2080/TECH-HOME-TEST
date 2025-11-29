<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'color',
        'icono',
        'estado'
    ];
    
    protected $casts = [
        'estado' => 'integer'
    ];
    
    public $timestamps = true;

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Cursos que pertenecen a esta categoría
     */
    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'categoria_id');
    }

    /**
     * Libros que pertenecen a esta categoría
     */
    public function libros(): HasMany
    {
        return $this->hasMany(Libro::class, 'categoria_id');
    }

    /**
     * Componentes que pertenecen a esta categoría
     */
    public function componentes(): HasMany
    {
        return $this->hasMany(Componente::class, 'categoria_id');
    }

    /**
     * Materiales que pertenecen a esta categoría
     */
    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class, 'categoria_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope para categorías por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para categorías activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 1);
    }

    /**
     * Scope para categorías de cursos
     */
    public function scopeCursos($query)
    {
        return $query->where('tipo', 'curso')->where('estado', 1);
    }

    /**
     * Scope para categorías de libros
     */
    public function scopeLibros($query)
    {
        return $query->where('tipo', 'libro')->where('estado', 1);
    }

    /**
     * Scope para categorías de componentes
     */
    public function scopeComponentes($query)
    {
        return $query->where('tipo', 'componente')->where('estado', 1);
    }

    /**
     * Scope para categorías de materiales
     */
    public function scopeMateriales($query)
    {
        return $query->where('tipo', 'material')->where('estado', 1);
    }

    // ==========================================
    // MÉTODOS ADICIONALES
    // ==========================================

    /**
     * Obtener el conteo de elementos por tipo
     */
    public function getCountByType(): array
    {
        return [
            'cursos' => $this->cursos()->where('estado', 'Publicado')->count(),
            'libros' => $this->libros()->where('estado', 1)->count(),
            'componentes' => $this->componentes()->where('estado', 'Activo')->count(),
            'materiales' => $this->materiales()->where('estado', 1)->count()
        ];
    }

    /**
     * Verificar si la categoría está en uso
     */
    public function isInUse(): bool
    {
        $counts = $this->getCountByType();
        return array_sum($counts) > 0;
    }

    /**
     * Obtener categorías con conteos
     */
    public static function withCounts($tipo = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::activas()->orderBy('nombre');
        
        if ($tipo) {
            $query->where('tipo', $tipo);
        }
        
        return $query->get()->map(function ($categoria) {
            $categoria->counts = $categoria->getCountByType();
            return $categoria;
        });
    }
}