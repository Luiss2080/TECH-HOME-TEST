<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgress extends Model
{
    use HasFactory;

    protected $table = 'student_progress';
    
    protected $fillable = [
        'usuario_id',
        'curso_id',
        'componente_id',
        'completado',
        'fecha_completado',
        'tiempo_dedicado',
        'puntuacion',
        'intentos'
    ];

    protected $casts = [
        'completado' => 'boolean',
        'fecha_completado' => 'datetime',
        'tiempo_dedicado' => 'integer',
        'puntuacion' => 'decimal:2',
        'intentos' => 'integer'
    ];

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación con el curso
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    /**
     * Relación con el componente
     */
    public function componente(): BelongsTo
    {
        return $this->belongsTo(Componente::class, 'componente_id');
    }

    /**
     * Scope para progreso completado
     */
    public function scopeCompletado($query)
    {
        return $query->where('completado', true);
    }

    /**
     * Scope para progreso no completado
     */
    public function scopePendiente($query)
    {
        return $query->where('completado', false);
    }
}