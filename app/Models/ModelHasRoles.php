<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModelHasRoles extends Model
{
    protected $table = 'model_has_roles';
    protected $primaryKey = null; // Esta tabla no tiene primary key único
    public $incrementing = false;
    
    protected $fillable = [
        'role_id',
        'model_type',
        'model_id'
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
     * Método estático para asignar rol a un modelo
     */
    public static function assignRole($modelType, $modelId, $roleId): bool
    {
        // Verificar si ya existe la relación
        $exists = DB::table('model_has_roles')
            ->where('role_id', $roleId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->exists();

        if ($exists) {
            return true; // Ya existe
        }

        // Insertar la nueva relación
        return DB::table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => $modelType,
            'model_id' => $modelId
        ]);
    }

    /**
     * Método estático para remover rol de un modelo
     */
    public static function removeRole($modelType, $modelId, $roleId): bool
    {
        $deleted = DB::table('model_has_roles')
            ->where('role_id', $roleId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->delete();

        return $deleted > 0;
    }

    /**
     * Obtener roles para un modelo específico
     */
    public static function getRolesForModel($modelType, $modelId): array
    {
        $roles = DB::table('roles as r')
            ->join('model_has_roles as mhr', 'r.id', '=', 'mhr.role_id')
            ->where('mhr.model_type', $modelType)
            ->where('mhr.model_id', $modelId)
            ->where('r.estado', 1)
            ->select('r.*')
            ->get()
            ->toArray();

        return array_map(function($role) {
            return (array) $role;
        }, $roles);
    }

    /**
     * Verificar si un modelo tiene un rol específico
     */
    public static function modelHasRole($modelType, $modelId, $roleId): bool
    {
        return DB::table('model_has_roles')
            ->where('role_id', $roleId)
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->exists();
    }

    /**
     * Obtener todos los modelos que tienen un rol específico
     */
    public static function getModelsWithRole($roleId, $modelType): array
    {
        $models = DB::table('model_has_roles')
            ->where('role_id', $roleId)
            ->where('model_type', $modelType)
            ->select('model_id')
            ->get()
            ->pluck('model_id')
            ->toArray();

        return $models;
    }

    /**
     * Limpiar todos los roles de un modelo
     */
    public static function clearRolesForModel($modelType, $modelId): bool
    {
        $deleted = DB::table('model_has_roles')
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->delete();

        return $deleted >= 0; // Retornar true incluso si no había roles que eliminar
    }
}