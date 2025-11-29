<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'name',
        'guard_name'
    ];
    
    public $timestamps = true;

    /**
     * Relación many-to-many con roles
     */
    public function roleModels(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }

    /**
     * Relación: roles que tienen este permiso (método legacy)
     */
    public function roles(): array
    {
        $db = \Illuminate\Support\Facades\DB::connection()->getPdo();
        $query = "SELECT r.* FROM roles r 
                  INNER JOIN role_has_permissions rhp ON r.id = rhp.role_id 
                  WHERE rhp.permission_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Relación: usuarios que tienen este permiso directamente
     */
    public function users(): array
    {
        $db = \Illuminate\Support\Facades\DB::connection()->getPdo();
        $query = "SELECT u.* FROM usuarios u 
                  INNER JOIN model_has_permissions mhp ON u.id = mhp.model_id 
                  WHERE mhp.permission_id = ? AND mhp.model_type = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$this->id, 'App\\Models\\User']);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Método para verificar si el permiso está asignado a un rol
     */
    public function isAssignedToRole($roleId): bool
    {
        return RoleHasPermissions::roleHasPermission($roleId, $this->id);
    }

    /**
     * Método para verificar si el permiso está asignado directamente a un usuario
     */
    public function isAssignedToUser($userId): bool
    {
        return ModelHasPermissions::modelHasPermission('App\\Models\\User', $userId, $this->id);
    }

    /**
     * Scope para permisos por guard
     */
    public function scopeByGuard($query, $guard = 'web')
    {
        return $query->where('guard_name', $guard);
    }

    /**
     * Scope para buscar por nombre
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Buscar permiso por nombre
     */
    public static function findByName(string $name, string $guardName = 'web'): ?self
    {
        return static::where('name', $name)->where('guard_name', $guardName)->first();
    }

    /**
     * Obtener todos los permisos ordenados por nombre
     */
    public static function getAllOrdered(): \Illuminate\Database\Eloquent\Collection
    {
        return static::orderBy('name')->get();
    }
}