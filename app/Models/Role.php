<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'roles';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relación con los usuarios (muchos a muchos)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Relación con los permisos (muchos a muchos)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
                    ->withTimestamps();
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()
                        ->where('nombre', $permission)
                        ->exists();
        }

        return $this->permissions()
                    ->where('id', $permission)
                    ->exists();
    }

    /**
     * Asignar permiso al rol
     */
    public function assignPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('nombre', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }

    /**
     * Revocar permiso del rol
     */
    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('nombre', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
    }

    /**
     * Scope para roles activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Verificar si el rol está activo
     */
    public function estaActivo()
    {
        return $this->estado === 'activo';
    }
}
