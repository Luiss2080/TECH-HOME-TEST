<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $table = 'enrollments';
    
    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'fecha_inscripcion'
    ];
    
    protected $casts = [
        'fecha_inscripcion' => 'datetime'
    ];

    /**
     * Estudiante inscrito
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    /**
     * Curso al que se inscribió
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }
}