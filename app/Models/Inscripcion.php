<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'fecha_inscripcion',
        'estado',
        'progreso',
        'fecha_completado',
        'calificacion_final',
        'certificado_generado'
    ];
    
    protected $casts = [
        'estudiante_id' => 'integer',
        'curso_id' => 'integer',
        'fecha_inscripcion' => 'datetime',
        'fecha_completado' => 'datetime',
        'progreso' => 'decimal:2',
        'calificacion_final' => 'decimal:2',
        'certificado_generado' => 'boolean'
    ];
    
    public $timestamps = false;

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Estudiante inscrito
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    /**
     * Curso al que está inscrito
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope para inscripciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para inscripciones completadas
     */
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completado');
    }

    /**
     * Scope por estudiante
     */
    public function scopePorEstudiante($query, $estudianteId)
    {
        return $query->where('estudiante_id', $estudianteId);
    }

    /**
     * Scope por curso
     */
    public function scopePorCurso($query, $cursoId)
    {
        return $query->where('curso_id', $cursoId);
    }

    // ==========================================
    // MÉTODOS ADICIONALES
    // ==========================================

    /**
     * Verificar si la inscripción está activa
     */
    public function isActive(): bool
    {
        return $this->estado === 'activo';
    }

    /**
     * Verificar si el curso está completado
     */
    public function isCompleted(): bool
    {
        return $this->estado === 'completado' || $this->progreso >= 100;
    }

    /**
     * Obtener porcentaje de progreso formateado
     */
    public function getProgresoFormateado(): string
    {
        return number_format($this->progreso, 1) . '%';
    }

    /**
     * Actualizar progreso
     */
    public function updateProgreso(float $nuevoProgreso): void
    {
        $this->progreso = min(100, max(0, $nuevoProgreso));
        
        // Si llega a 100%, marcar como completado
        if ($this->progreso >= 100 && $this->estado === 'activo') {
            $this->estado = 'completado';
            $this->fecha_completado = now();
        }
        
        $this->save();
    }

    /**
     * Obtener estado formateado
     */
    public function getEstadoFormateado(): string
    {
        return match($this->estado) {
            'activo' => '📚 En progreso',
            'completado' => '✅ Completado',
            'pausado' => '⏸️ Pausado',
            'cancelado' => '❌ Cancelado',
            default => ucfirst($this->estado)
        };
    }

    /**
     * Obtener clase CSS para el estado
     */
    public function getEstadoClass(): string
    {
        return match($this->estado) {
            'activo' => 'badge-primary',
            'completado' => 'badge-success',
            'pausado' => 'badge-warning',
            'cancelado' => 'badge-danger',
            default => 'badge-secondary'
        };
    }
}