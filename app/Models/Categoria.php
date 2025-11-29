<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categories';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'color',
        'icono',
        'estado'
    ];
    
    protected $casts = [
        'estado' => 'boolean',
        'fecha_creacion' => 'datetime'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Cursos de esta categoría
     */
    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'categoria_id');
    }

    /**
     * Libros de esta categoría
     */
    public function libros(): HasMany
    {
        return $this->hasMany(Libro::class, 'categoria_id');
    }

    /**
     * Componentes de esta categoría
     */
    public function componentes(): HasMany
    {
        return $this->hasMany(Componente::class, 'categoria_id');
    }

    /**
     * Materiales de esta categoría
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
}
