<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    
    // Constantes para timestamps personalizados
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
    
    protected $fillable = [
        'nombre',
        'apellido', 
        'email',
        'password',
        'telefono',
        'fecha_nacimiento',
        'avatar',
        'estado',
        'intentos_fallidos',
        'bloqueado_hasta'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'fecha_nacimiento' => 'date',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'bloqueado_hasta' => 'datetime',
        'estado' => 'integer',
        'intentos_fallidos' => 'integer'
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    // ==========================================
    // MÉTODOS PARA ROLES Y PERMISOS (HasRoles)
    // ==========================================

    /**
     * Obtener todos los roles del usuario
     */
    public function roles(): array
    {
        return ModelHasRoles::getRolesForModel('App\\Models\\User', $this->id);
    }

    /**
     * Relación many-to-many con roles
     */
    public function roleModels(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
                    ->wherePivot('model_type', 'App\\Models\\User');
    }

    /**
     * Obtener todos los permisos del usuario (directos + a través de roles)
     */
    public function permissions(): array
    {
        // Usar cache para evitar consultas repetidas
        return cache()->remember("user_permissions_{$this->id}", 300, function () {
            $directPermissions = ModelHasPermissions::getPermissionsForModel('App\\Models\\User', $this->id);
            $rolePermissions = $this->getPermissionsViaRoles();
            
            // Combinar y eliminar duplicados usando array_unique con SORT_REGULAR
            $allPermissions = array_merge($directPermissions, $rolePermissions);
            return array_values(array_unique($allPermissions, SORT_REGULAR));
        });
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($role): bool
    {
        if (is_numeric($role)) {
            return ModelHasRoles::modelHasRole('App\\Models\\User', $this->id, $role);
        }
        
        $roleId = cache()->remember("role_id_{$role}", 3600, function () use ($role) {
            return $this->getRoleIdByName($role);
        });
        
        return $roleId && ModelHasRoles::modelHasRole('App\\Models\\User', $this->id, $roleId);
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function hasAnyRole($roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Verificar si el usuario tiene todos los roles especificados
     */
    public function hasAllRoles($roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermission($permission): bool
    {
        $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);
        if (!$permissionId) return false;

        // Verificar permiso directo
        if (ModelHasPermissions::modelHasPermission('App\\Models\\User', $this->id, $permissionId)) {
            return true;
        }

        // Verificar permiso a través de roles
        return $this->hasPermissionThroughRole($permissionId);
    }

    /**
     * Asignar rol al usuario
     */
    public function assignRole($roleId): bool
    {
        if ($this->hasRole($roleId)) {
            return true; // Ya tiene el rol
        }

        return ModelHasRoles::assignRole('App\\Models\\User', $this->id, $roleId);
    }

    /**
     * Remover rol del usuario
     */
    public function removeRole($roleId): bool
    {
        return ModelHasRoles::removeRole('App\\Models\\User', $this->id, $roleId);
    }

    /**
     * Sincronizar roles del usuario
     */
    public function syncRoles(array $roleIds): bool
    {
        try {
            // Remover todos los roles actuales
            $currentRoles = $this->roles();
            foreach ($currentRoles as $role) {
                $this->removeRole($role['id']);
            }

            // Asignar nuevos roles
            foreach ($roleIds as $roleId) {
                $this->assignRole($roleId);
            }

            return true;
        } catch (\Exception $e) {
            error_log('Error sincronizando roles: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario está bloqueado
     */
    public function isBlocked(): bool
    {
        return $this->bloqueado_hasta && $this->bloqueado_hasta > now();
    }

    /**
     * Obtener tiempo restante de bloqueo en minutos
     */
    public function getBlockTimeRemaining(): int
    {
        if (!$this->isBlocked()) {
            return 0;
        }

        return now()->diffInMinutes($this->bloqueado_hasta);
    }

    /**
     * Obtener nombre completo del usuario
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    /**
     * Scope para usuarios no bloqueados
     */
    public function scopeNoBloqueados($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('bloqueado_hasta')
                  ->orWhere('bloqueado_hasta', '<=', now());
        });
    }

    // ==========================================
    // MÉTODOS AUXILIARES PRIVADOS
    // ==========================================

    private function getRoleIdByName(string $roleName): ?int
    {
        $role = Role::where('nombre', $roleName)->first();
        return $role ? $role->id : null;
    }

    private function getPermissionIdByName(string $permissionName): ?int
    {
        $permission = Permission::where('nombre', $permissionName)->first();
        return $permission ? $permission->id : null;
    }

    private function getPermissionsViaRoles(): array
    {
        $roles = $this->roles();
        $permissions = [];

        foreach ($roles as $role) {
            $rolePermissions = RoleHasPermissions::getPermissionsForRole($role['id']);
            $permissions = array_merge($permissions, $rolePermissions);
        }

        return $permissions;
    }

    private function hasPermissionThroughRole(int $permissionId): bool
    {
        $roles = $this->roles();

        foreach ($roles as $role) {
            if (RoleHasPermissions::roleHasPermission($role['id'], $permissionId)) {
                return true;
            }
        }

        return false;
    }
}
