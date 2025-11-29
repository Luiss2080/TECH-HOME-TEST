<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RoleHasPermissions extends Model
{
    protected $table = 'role_has_permissions';
    protected $primaryKey = null; // Esta tabla no tiene primary key único
    public $incrementing = false;
    
    protected $fillable = [
        'role_id',
        'permission_id'
    ];
    
    public $timestamps = false;

    /**
     * Relación con el rol
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Relación con el permiso
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    /**
     * Método estático para asignar permiso a un rol
     */
    public static function assignPermissionToRole($roleId, $permissionId): bool
    {
        // Verificar si ya existe la relación
        $exists = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->exists();

        if ($exists) {
            return true; // Ya existe
        }

        // Insertar la nueva relación
        return DB::table('role_has_permissions')->insert([
            'role_id' => $roleId,
            'permission_id' => $permissionId
        ]);
    }

    /**
     * Método estático para remover permiso de un rol
     */
    public static function removePermissionFromRole($roleId, $permissionId): bool
    {
        $deleted = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->delete();

        return $deleted > 0;
    }

    /**
     * Obtener permisos para un rol específico
     */
    public static function getPermissionsForRole($roleId): array
    {
        $permissions = DB::table('permissions as p')
            ->join('role_has_permissions as rhp', 'p.id', '=', 'rhp.permission_id')
            ->where('rhp.role_id', $roleId)
            ->select('p.*')
            ->get()
            ->toArray();

        return array_map(function($permission) {
            return (array) $permission;
        }, $permissions);
    }

    /**
     * Verificar si un rol tiene un permiso específico
     */
    public static function roleHasPermission($roleId, $permissionId): bool
    {
        return DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->exists();
    }

    /**
     * Obtener todos los roles que tienen un permiso específico
     */
    public static function getRolesWithPermission($permissionId): array
    {
        $roles = DB::table('role_has_permissions')
            ->where('permission_id', $permissionId)
            ->select('role_id')
            ->get()
            ->pluck('role_id')
            ->toArray();

        return $roles;
    }

    /**
     * Limpiar todos los permisos de un rol
     */
    public static function clearPermissionsForRole($roleId): bool
    {
        $deleted = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->delete();

        return $deleted >= 0; // Retornar true incluso si no había permisos que eliminar
    }

    /**
     * Sincronizar permisos para un rol (eliminar todos y agregar los nuevos)
     */
    public static function syncPermissionsForRole($roleId, array $permissionIds): bool
    {
        // Primero eliminar todos los permisos existentes
        static::clearPermissionsForRole($roleId);

        // Luego agregar los nuevos permisos
        foreach ($permissionIds as $permissionId) {
            static::assignPermissionToRole($roleId, $permissionId);
        }

        return true;
    }
}