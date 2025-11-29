<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Componente extends Model
{
    protected $table = 'componentes';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'curso_id',
        'categoria_id',
        'tipo',
        'contenido_url',
        'orden',
        'duracion_estimada',
        'estado',
        'precio',
        'stock_actual',
        'stock_minimo',
        'marca',
        'modelo',
        'especificaciones',
        'imagen_url'
    ];
    
    protected $casts = [
        'curso_id' => 'integer',
        'categoria_id' => 'integer',
        'orden' => 'integer',
        'duracion_estimada' => 'integer',
        'precio' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];
    
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Curso al que pertenece el componente
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    /**
     * Categoría del componente
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Materiales relacionados con este componente
     */
    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class, 'componente_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope para componentes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'Activo');
    }

    /**
     * Scope para componentes por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para componentes con stock bajo
     */
    public function scopeStockBajo($query)
    {
        return $query->whereRaw('stock_actual <= stock_minimo');
    }

    /**
     * Scope para componentes sin stock
     */
    public function scopeSinStock($query)
    {
        return $query->where('stock_actual', 0);
    }

    /**
     * Scope para ordenar por orden de curso
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden', 'asc');
    }

    /**
     * Scope para buscar por texto
     */
    public function scopeBuscar($query, $texto)
    {
        return $query->where(function($q) use ($texto) {
            $q->where('nombre', 'like', "%{$texto}%")
              ->orWhere('descripcion', 'like', "%{$texto}%")
              ->orWhere('marca', 'like', "%{$texto}%")
              ->orWhere('modelo', 'like', "%{$texto}%");
        });
    }

    // ==========================================
    // MÉTODOS ADICIONALES
    // ==========================================

    /**
     * Verificar si el componente tiene stock bajo
     */
    public function hasLowStock(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    /**
     * Verificar si el componente está sin stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_actual <= 0;
    }

    /**
     * Obtener el estado del stock
     */
    public function getStockStatus(): string
    {
        if ($this->isOutOfStock()) {
            return 'Sin Stock';
        } elseif ($this->hasLowStock()) {
            return 'Stock Bajo';
        }
        
        return 'Stock Disponible';
    }

    /**
     * Obtener clase CSS para el estado del stock
     */
    public function getStockStatusClass(): string
    {
        if ($this->isOutOfStock()) {
            return 'badge-danger';
        } elseif ($this->hasLowStock()) {
            return 'badge-warning';
        }
        
        return 'badge-success';
    }

    /**
     * Obtener nombre completo con marca y modelo
     */
    public function getFullName(): string
    {
        $name = $this->nombre;
        
        if ($this->marca) {
            $name = $this->marca . ' ' . $name;
        }
        
        if ($this->modelo) {
            $name .= ' ' . $this->modelo;
        }
        
        return $name;
    }

    /**
     * Obtener duración formateada
     */
    public function getDuracionFormateada(): string
    {
        if (!$this->duracion_estimada) {
            return 'No especificada';
        }

        $minutes = $this->duracion_estimada;
        
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes == 0) {
            return $hours . 'h';
        }
        
        return $hours . 'h ' . $remainingMinutes . 'min';
    }

    /**
     * Obtener precio formateado
     */
    public function getPrecioFormateado(): string
    {
        if ($this->precio == 0) {
            return 'Gratuito';
        }
        
        return '$' . number_format($this->precio, 0, ',', '.');
    }

    /**
     * Actualizar stock
     */
    public function updateStock(int $cantidad, string $tipo = 'entrada'): bool
    {
        if ($tipo === 'salida') {
            if ($this->stock_actual < $cantidad) {
                return false; // No hay suficiente stock
            }
            $this->stock_actual -= $cantidad;
        } else {
            $this->stock_actual += $cantidad;
        }
        
        return $this->save();
    }

    /**
     * Obtener el siguiente orden para el curso
     */
    public static function getNextOrderForCourse($cursoId): int
    {
        $maxOrder = static::where('curso_id', $cursoId)->max('orden');
        return $maxOrder ? $maxOrder + 1 : 1;
    }

    /**
     * Obtener estadísticas del componente
     */
    public function getStats(): array
    {
        return [
            'valor_stock' => $this->stock_actual * $this->precio,
            'necesita_restock' => $this->hasLowStock(),
            'total_materiales' => $this->materiales()->count(),
            'stock_status' => $this->getStockStatus()
        ];
    }
}