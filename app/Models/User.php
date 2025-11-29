<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token'
    ];
    
    protected $hidden = [
        'password',
        'remember_token'
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed'
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Cursos como docente
     */
    public function cursosDocente(): HasMany
    {
        return $this->hasMany(Curso::class, 'docente_id');
    }

    /**
     * Inscripciones como estudiante
     */
    public function inscripciones(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    // ==========================================
    // MÉTODOS DE CONVENIENCIA
    // ==========================================

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($role): bool
    {
        // Implementación básica - se puede expandir cuando se implementen los roles
        return false;
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermissionTo($permission): bool
    {
        // Implementación básica - se puede expandir cuando se implementen los permisos
        return false;
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}