<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Laboratorio extends Model
{
    use HasFactory;

    protected $table = 'laboratorios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'capacidad',
        'equipamiento',
        'estado',
        'disponibilidad',
        'responsable',
        'horarios'
    ];

    protected $casts = [
        'equipamiento' => 'array',
        'capacidad' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope para laboratorios disponibles
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'activo')
                    ->where('disponibilidad', 'disponible');
    }

    // Scope para laboratorios activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // Relación con cursos
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'curso_laboratorio', 'laboratorio_id', 'curso_id');
    }

    // Atributo computado para obtener estado formateado
    public function getEstadoFormateadoAttribute()
    {
        return ucfirst($this->estado);
    }

    // Atributo computado para verificar si está disponible
    public function getEstaDisponibleAttribute()
    {
        return $this->estado === 'activo' && $this->disponibilidad === 'disponible';
    }
}
