<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Libro extends Model
{
    protected $table = 'books';
    
    protected $fillable = [
        'titulo',
        'autor',
        'descripcion',
        'categoria_id',
        'isbn',
        'paginas',
        'editorial',
        'año_publicacion',
        'imagen_portada',
        'archivo_pdf',
        'enlace_externo',
        'tamaño_archivo',
        'stock',
        'stock_minimo',
        'precio',
        'estado',
        'descargas',
        'es_gratuito'
    ];
    
    protected $casts = [
        'precio' => 'decimal:2',
        'es_gratuito' => 'boolean',
        'paginas' => 'integer',
        'tamaño_archivo' => 'integer',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'descargas' => 'integer',
        'año_publicacion' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];
    
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Categoría del libro
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Descargas del libro
     */
    public function bookDownloads(): HasMany
    {
        return $this->hasMany(BookDownload::class, 'libro_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope para libros disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'Disponible');
    }

    /**
     * Scope para libros gratuitos
     */
    public function scopeGratuitos($query)
    {
        return $query->where('es_gratuito', true);
    }

    /**
     * Scope para libros por categoría
     */
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    /**
     * Scope para búsqueda
     */
    public function scopeBuscar($query, $texto)
    {
        if (empty($texto)) return $query;
        
        $texto = trim($texto);
        return $query->where(function($q) use ($texto) {
            $q->where('titulo', 'like', "%{$texto}%")
              ->orWhere('autor', 'like', "%{$texto}%")
              ->orWhere('descripcion', 'like', "%{$texto}%")
              ->orWhere('isbn', 'like', "%{$texto}%");
        });
    }

    // ==========================================
    // MÉTODOS ADICIONALES
    // ==========================================

    /**
     * Incrementar contador de descargas
     */
    public function incrementarDescargas(): void
    {
        $this->increment('descargas');
    }

    /**
     * Verificar si está disponible para descarga
     */
    public function estaDisponible(): bool
    {
        return $this->estado === 'Disponible' && $this->stock > 0;
    }

    /**
     * Obtener tamaño formateado del archivo
     */
    public function getTamañoFormateadoAttribute(): string
    {
        if ($this->tamaño_archivo == 0) {
            return 'N/A';
        }
        
        $bytes = $this->tamaño_archivo;
        
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}