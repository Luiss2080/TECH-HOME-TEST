<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Libro extends Model
{
    protected $table = 'libros';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'titulo',
        'descripcion',
        'autor',
        'editorial',
        'isbn',
        'categoria_id',
        'archivo_url',
        'imagen_portada',
        'precio',
        'es_gratuito',
        'estado',
        'autor_id',
        'fecha_publicacion',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    
    protected $casts = [
        'categoria_id' => 'integer',
        'autor_id' => 'integer',
        'precio' => 'decimal:2',
        'es_gratuito' => 'boolean',
        'estado' => 'integer',
        'fecha_publicacion' => 'date',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];
    
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    
    public $timestamps = true;

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
     * Autor/Usuario que subió el libro
     */
    public function autorUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    /**
     * Descargas del libro
     */
    public function descargas(): HasMany
    {
        return $this->hasMany(BookDownload::class, 'libro_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope para libros publicados/activos
     */
    public function scopePublicados($query)
    {
        return $query->where('estado', 1);
    }

    /**
     * Scope para libros gratuitos
     */
    public function scopeGratuitos($query)
    {
        return $query->where('es_gratuito', true);
    }

    /**
     * Scope para libros de pago
     */
    public function scopeDePago($query)
    {
        return $query->where('es_gratuito', false);
    }

    /**
     * Scope para libros por categoría
     */
    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    /**
     * Scope para libros por autor (texto)
     */
    public function scopePorAutor($query, $autor)
    {
        return $query->where('autor', 'like', "%{$autor}%");
    }

    /**
     * Scope para libros por editorial
     */
    public function scopePorEditorial($query, $editorial)
    {
        return $query->where('editorial', 'like', "%{$editorial}%");
    }

    /**
     * Scope para búsqueda por texto
     */
    public function scopeBuscar($query, $texto)
    {
        return $query->where(function($q) use ($texto) {
            $q->where('titulo', 'like', "%{$texto}%")
              ->orWhere('descripcion', 'like', "%{$texto}%")
              ->orWhere('autor', 'like', "%{$texto}%")
              ->orWhere('editorial', 'like', "%{$texto}%");
        });
    }

    /**
     * Scope para ordenar por popularidad (más descargas)
     */
    public function scopePopular($query)
    {
        return $query->withCount('descargas')->orderBy('descargas_count', 'desc');
    }

    /**
     * Scope para libros recientes
     */
    public function scopeRecientes($query, $days = 30)
    {
        return $query->where('fecha_creacion', '>=', now()->subDays($days))
                     ->orderBy('fecha_creacion', 'desc');
    }

    // ==========================================
    // MÉTODOS ADICIONALES
    // ==========================================

    /**
     * Verificar si es un archivo externo (URL)
     */
    public function isExternalFile(): bool
    {
        return $this->archivo_url && str_starts_with($this->archivo_url, 'http');
    }

    /**
     * Verificar si es un archivo local
     */
    public function isLocalFile(): bool
    {
        return $this->archivo_url && str_starts_with($this->archivo_url, '/storage/');
    }

    /**
     * Obtener el nombre del archivo
     */
    public function getFileName(): ?string
    {
        if (!$this->archivo_url) {
            return null;
        }

        if ($this->isExternalFile()) {
            return basename(parse_url($this->archivo_url, PHP_URL_PATH));
        }

        return basename($this->archivo_url);
    }

    /**
     * Obtener el tamaño del archivo (si es local)
     */
    public function getFileSize(): ?string
    {
        if (!$this->isLocalFile()) {
            return null;
        }

        $filePath = str_replace('/storage/', '', $this->archivo_url);
        
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            return null;
        }

        $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($filePath);
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    /**
     * Obtener total de descargas
     */
    public function getTotalDownloads(): int
    {
        return $this->descargas()->count();
    }

    /**
     * Obtener precio formateado
     */
    public function getPrecioFormateado(): string
    {
        if ($this->es_gratuito || $this->precio == 0) {
            return 'Gratuito';
        }
        
        return '$' . number_format($this->precio, 0, ',', '.');
    }

    /**
     * Obtener estado formateado
     */
    public function getEstadoFormateado(): string
    {
        return $this->estado == 1 ? 'Publicado' : 'Borrador';
    }

    /**
     * Obtener clase CSS para el estado
     */
    public function getEstadoClass(): string
    {
        return $this->estado == 1 ? 'badge-success' : 'badge-secondary';
    }

    /**
     * Verificar si un usuario puede descargar este libro
     */
    public function canBeDownloadedBy($userId): bool
    {
        // Si es gratuito, cualquier usuario autenticado puede descargarlo
        if ($this->es_gratuito) {
            return true;
        }

        // Si es de pago, verificar si el usuario lo ha comprado
        // TODO: Implementar lógica de compras/acceso
        return false;
    }

    /**
     * Registrar una descarga
     */
    public function registerDownload($userId): void
    {
        $this->descargas()->create([
            'usuario_id' => $userId,
            'fecha_descarga' => now(),
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Obtener libros relacionados (misma categoría)
     */
    public function getRelatedBooks($limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('categoria_id', $this->categoria_id)
                     ->where('id', '!=', $this->id)
                     ->where('estado', 1)
                     ->inRandomOrder()
                     ->limit($limit)
                     ->get();
    }

    /**
     * Obtener estadísticas del libro
     */
    public function getStats(): array
    {
        return [
            'total_descargas' => $this->getTotalDownloads(),
            'tamaño_archivo' => $this->getFileSize(),
            'tipo_archivo' => $this->isExternalFile() ? 'Externo' : 'Local',
            'estado' => $this->getEstadoFormateado(),
            'precio' => $this->getPrecioFormateado()
        ];
    }

    /**
     * Obtener URL de descarga segura
     */
    public function getSecureDownloadUrl(): string
    {
        return route('libros.download', $this->id);
    }
}