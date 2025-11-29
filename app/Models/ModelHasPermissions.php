<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModelHasPermissions extends Model
{
    protected $table = 'model_has_permissions';
    protected $primaryKey = null; // Esta tabla no tiene primary key único
    public $incrementing = false;
    
    protected $fillable = [
        'permission_id',
        'model_type',
        'model_id'
    ];
    
    public $timestamps = false;

    /**
     * Relación con el permiso
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    /**
     * Método estático para asignar permiso a un modelo
     */
    public static function assignPermission($modelType, $modelId, $permissionId): bool
    {
        // Verificar si ya existe la relación
        $exists = DB::table('model_has_permissions')
            ->where('permission_id', $permissionId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->exists();

        if ($exists) {
            return true; // Ya existe
        }

        // Insertar la nueva relación
        return DB::table('model_has_permissions')->insert([
            'permission_id' => $permissionId,
            'model_type' => $modelType,
            'model_id' => $modelId
        ]);
    }

    /**
     * Método estático para remover permiso de un modelo
     */
    public static function removePermission($modelType, $modelId, $permissionId): bool
    {
        $deleted = DB::table('model_has_permissions')
            ->where('permission_id', $permissionId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->delete();

        return $deleted > 0;
    }

    /**
     * Obtener permisos para un modelo específico
     */
    public static function getPermissionsForModel($modelType, $modelId): array
    {
        $permissions = DB::table('permissions as p')
            ->join('model_has_permissions as mhp', 'p.id', '=', 'mhp.permission_id')
            ->where('mhp.model_type', $modelType)
            ->where('mhp.model_id', $modelId)
            ->select('p.*')
            ->get()
            ->toArray();

        return array_map(function($permission) {
            return (array) $permission;
        }, $permissions);
    }

    /**
     * Verificar si un modelo tiene un permiso específico
     */
    public static function modelHasPermission($modelType, $modelId, $permissionId): bool
    {
        return DB::table('model_has_permissions')
            ->where('permission_id', $permissionId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->exists();
    }

    /**
     * Obtener todos los modelos que tienen un permiso específico
     */
    public static function getModelsWithPermission($permissionId, $modelType): array
    {
        $models = DB::table('model_has_permissions')
            ->where('permission_id', $permissionId)
            ->where('model_type', $modelType)
            ->select('model_id')
            ->get()
            ->pluck('model_id')
            ->toArray();

        return $models;
    }

    /**
     * Limpiar todos los permisos de un modelo
     */
    public static function clearPermissionsForModel($modelType, $modelId): bool
    {
        $deleted = DB::table('model_has_permissions')
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->delete();

        return $deleted >= 0; // Retornar true incluso si no había permisos que eliminar
    }

    /**
     * Sincronizar permisos para un modelo (eliminar todos y agregar los nuevos)
     */
    public static function syncPermissionsForModel($modelType, $modelId, array $permissionIds): bool
    {
        // Primero eliminar todos los permisos existentes
        static::clearPermissionsForModel($modelType, $modelId);

        // Luego agregar los nuevos permisos
        foreach ($permissionIds as $permissionId) {
            static::assignPermission($modelType, $modelId, $permissionId);
        }

        return true;
    }
}