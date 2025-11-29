<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];
    
    protected $casts = [
        'estado' => 'integer'
    ];
    
    public $timestamps = false;

    /**
     * Relación many-to-many con usuarios
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id')
                    ->wherePivot('model_type', 'App\\Models\\User');
    }

    /**
     * Relación many-to-many con permisos
     */
    public function permissionModels(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    /**
     * Obtener permisos del rol usando el sistema custom
     */
    public function permissions(): array
    {
        $permissions = RoleHasPermissions::getPermissionsForRole($this->id);
        return is_array($permissions) ? $permissions : [];
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function hasPermissionTo($permission): bool
    {
        $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);
        if (!$permissionId) return false;

        return RoleHasPermissions::roleHasPermission($this->id, $permissionId);
    }

    /**
     * Asignar un permiso a este rol
     */
    public function givePermissionTo($permission): self
    {
        $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);

        if (!$permissionId) {
            throw new \Exception("Permiso no encontrado: {$permission}");
        }

        RoleHasPermissions::assignPermissionToRole($this->id, $permissionId);
        return $this;
    }

    /**
     * Remover un permiso de este rol
     */
    public function revokePermissionTo($permission): self
    {
        $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);

        if (!$permissionId) {
            return $this;
        }

        RoleHasPermissions::removePermissionFromRole($this->id, $permissionId);
        return $this;
    }

    /**
     * Sincronizar permisos (remover todos y asignar los nuevos)
     */
    public function syncPermissions($permissions): self
    {
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        $permissionIds = [];
        foreach ($permissions as $permission) {
            $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);
            if ($permissionId) {
                $permissionIds[] = $permissionId;
            }
        }

        RoleHasPermissions::syncPermissionsForRole($this->id, $permissionIds);
        return $this;
    }

    /**
     * Buscar rol por nombre
     */
    public static function findByName(string $name): ?self
    {
        return static::where('nombre', $name)->first();
    }

    /**
     * Scope para roles activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // ==========================================
    // MÉTODOS AUXILIARES PRIVADOS
    // ==========================================

    private function getPermissionIdByName(string $permissionName): ?int
    {
        $permission = Permission::where('nombre', $permissionName)->first();
        return $permission ? $permission->id : null;
    }
}