<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'materials';
    
    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'ruta_archivo',
        'url_externa',
        'curso_id',
        'componente_id',
        'categoria_id',
        'orden',
        'estado',
        'tamaño_archivo',
        'extension',
        'es_descargable'
    ];

    protected $casts = [
        'orden' => 'integer',
        'tamaño_archivo' => 'integer',
        'es_descargable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relación con el curso
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    /**
     * Relación con el componente
     */
    public function componente()
    {
        return $this->belongsTo(Componente::class, 'componente_id');
    }

    /**
     * Relación con la categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Scope para materiales activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden');
    }

    /**
     * Scope para materiales descargables
     */
    public function scopeDescargables($query)
    {
        return $query->where('es_descargable', true);
    }

    /**
     * Obtener URL del archivo
     */
    public function getUrlArchivoAttribute()
    {
        if ($this->url_externa) {
            return $this->url_externa;
        }
        
        if ($this->ruta_archivo) {
            return asset('storage/materiales/' . $this->ruta_archivo);
        }
        
        return null;
    }

    /**
     * Obtener tamaño formateado
     */
    public function getTamañoFormateadoAttribute()
    {
        if (!$this->tamaño_archivo) {
            return 'No especificado';
        }

        $bytes = $this->tamaño_archivo;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Obtener tipo capitalizado
     */
    public function getTipoCapitalizadoAttribute()
    {
        return ucfirst($this->tipo);
    }

    /**
     * Verificar si es archivo
     */
    public function esArchivo()
    {
        return !empty($this->ruta_archivo);
    }

    /**
     * Verificar si es enlace externo
     */
    public function esEnlaceExterno()
    {
        return !empty($this->url_externa);
    }
}
