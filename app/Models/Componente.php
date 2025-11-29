<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Componente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'components';
    
    protected $fillable = [
        'titulo',
        'descripcion',
        'contenido',
        'curso_id',
        'categoria_id',
        'tipo',
        'orden',
        'estado',
        'duracion_minutos',
        'recursos_adicionales'
    ];

    protected $casts = [
        'orden' => 'integer',
        'duracion_minutos' => 'integer',
        'recursos_adicionales' => 'array',
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
     * Relación con la categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Relación con los materiales
     */
    public function materiales()
    {
        return $this->hasMany(Material::class, 'componente_id');
    }

    /**
     * Scope para componentes activos
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
     * Obtener duración formateada
     */
    public function getDuracionFormateadaAttribute()
    {
        if (!$this->duracion_minutos) {
            return 'No especificada';
        }

        $horas = floor($this->duracion_minutos / 60);
        $minutos = $this->duracion_minutos % 60;

        if ($horas > 0 && $minutos > 0) {
            return $horas . 'h ' . $minutos . 'm';
        } elseif ($horas > 0) {
            return $horas . 'h';
        } else {
            return $minutos . 'm';
        }
    }

    /**
     * Obtener tipo capitalizado
     */
    public function getTipoCapitalizadoAttribute()
    {
        return ucfirst($this->tipo);
    }
}
