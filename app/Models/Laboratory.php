<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laboratory extends Model
{
    use HasFactory;

    protected $table = 'laboratories';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'docente_id',
        'capacidad_maxima',
        'ubicacion',
        'equipamiento',
        'software_requerido',
        'estado',
        'imagen'
    ];

    protected $casts = [
        'capacidad_maxima' => 'integer',
        'equipamiento' => 'array',
        'software_requerido' => 'array'
    ];

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    /**
     * Relación con categoría
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Relación con docente responsable
     */
    public function docente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    /**
     * Cursos que usan este laboratorio
     */
    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'laboratorio_id');
    }

    /**
     * Scope para laboratorios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para laboratorios disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Verificar disponibilidad
     */
    public function estaDisponible()
    {
        return $this->estado === 'activo';
    }

    /**
     * Obtener ocupación actual
     */
    public function getOcupacionActual()
    {
        // Implementar lógica de ocupación basada en inscripciones activas
        return $this->cursos()
                   ->whereHas('enrollments', function($query) {
                       $query->where('estado', 'activo');
                   })->count();
    }
}