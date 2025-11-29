<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    protected $table = 'materiales';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'archivo_url',
        'curso_id',
        'componente_id',
        'categoria_id',
        'tamaño_archivo',
        'formato',
        'estado',
        'orden',
        'es_descargable',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    
    protected $casts = [
        'curso_id' => 'integer',
        'componente_id' => 'integer',
        'categoria_id' => 'integer',
        'tamaño_archivo' => 'integer',
        'estado' => 'integer',
        'orden' => 'integer',
        'es_descargable' => 'boolean',
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
     * Curso al que pertenece el material
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    /**
     * Componente al que pertenece el material
     */
    public function componente(): BelongsTo
    {
        return $this->belongsTo(Componente::class, 'componente_id');
    }

    /**
     * Categoría del material
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope para materiales activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    /**
     * Scope para materiales por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para materiales descargables
     */
    public function scopeDescargables($query)
    {
        return $query->where('es_descargable', true);
    }

    /**
     * Scope para materiales de un curso
     */
    public function scopeDeCurso($query, $cursoId)
    {
        return $query->where('curso_id', $cursoId);
    }

    /**
     * Scope para materiales de un componente
     */
    public function scopeDeComponente($query, $componenteId)
    {
        return $query->where('componente_id', $componenteId);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden', 'asc');
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
     * Obtener tamaño del archivo formateado
     */
    public function getFormattedFileSize(): ?string
    {
        if (!$this->tamaño_archivo) {
            return 'N/A';
        }

        $bytes = $this->tamaño_archivo;
        
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
     * Obtener icono basado en el tipo de archivo
     */
    public function getFileIcon(): string
    {
        if (!$this->formato) {
            return 'fas fa-file';
        }

        return match(strtolower($this->formato)) {
            'pdf' => 'fas fa-file-pdf',
            'doc', 'docx' => 'fas fa-file-word',
            'xls', 'xlsx' => 'fas fa-file-excel',
            'ppt', 'pptx' => 'fas fa-file-powerpoint',
            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image',
            'mp4', 'avi', 'mov' => 'fas fa-file-video',
            'mp3', 'wav', 'ogg' => 'fas fa-file-audio',
            'zip', 'rar', '7z' => 'fas fa-file-archive',
            'txt' => 'fas fa-file-alt',
            'html', 'css', 'js', 'php' => 'fas fa-file-code',
            default => 'fas fa-file'
        };
    }

    /**
     * Obtener color del tipo de material
     */
    public function getTipoColor(): string
    {
        return match($this->tipo) {
            'documento' => 'primary',
            'video' => 'danger',
            'audio' => 'warning',
            'imagen' => 'success',
            'presentacion' => 'info',
            'ejercicio' => 'secondary',
            'enlace' => 'dark',
            default => 'primary'
        };
    }

    /**
     * Obtener tipo formateado
     */
    public function getTipoFormateado(): string
    {
        return match($this->tipo) {
            'documento' => '📄 Documento',
            'video' => '🎥 Video',
            'audio' => '🎵 Audio',
            'imagen' => '🖼️ Imagen',
            'presentacion' => '📊 Presentación',
            'ejercicio' => '📝 Ejercicio',
            'enlace' => '🔗 Enlace',
            default => ucfirst($this->tipo)
        };
    }

    /**
     * Verificar si se puede previsualizar en el navegador
     */
    public function canBePreviewedInBrowser(): bool
    {
        if (!$this->formato) {
            return false;
        }

        $previewableFormats = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'mp3', 'wav'];
        return in_array(strtolower($this->formato), $previewableFormats);
    }

    /**
     * Obtener URL de preview
     */
    public function getPreviewUrl(): ?string
    {
        if (!$this->canBePreviewedInBrowser()) {
            return null;
        }

        return route('materiales.preview', $this->id);
    }

    /**
     * Obtener URL de descarga
     */
    public function getDownloadUrl(): string
    {
        return route('materiales.download', $this->id);
    }

    /**
     * Obtener el siguiente orden para el curso/componente
     */
    public static function getNextOrder($cursoId, $componenteId = null): int
    {
        $query = static::where('curso_id', $cursoId);
        
        if ($componenteId) {
            $query->where('componente_id', $componenteId);
        } else {
            $query->whereNull('componente_id');
        }
        
        $maxOrder = $query->max('orden');
        return $maxOrder ? $maxOrder + 1 : 1;
    }

    /**
     * Actualizar tamaño del archivo si es local
     */
    public function updateFileSize(): void
    {
        if ($this->isLocalFile()) {
            $filePath = str_replace('/storage/', '', $this->archivo_url);
            
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                $this->tamaño_archivo = \Illuminate\Support\Facades\Storage::disk('public')->size($filePath);
                $this->save();
            }
        }
    }

    /**
     * Obtener estadísticas del material
     */
    public function getStats(): array
    {
        return [
            'tamaño_formateado' => $this->getFormattedFileSize(),
            'tipo_formateado' => $this->getTipoFormateado(),
            'es_externo' => $this->isExternalFile(),
            'se_puede_previsualizar' => $this->canBePreviewedInBrowser(),
            'formato' => strtoupper($this->formato ?: 'N/A')
        ];
    }
}