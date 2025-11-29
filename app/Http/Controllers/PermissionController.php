<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PermissionController extends Controller
{
    /**
     * Panel principal de gestión de permisos
     */
    public function index(): View
    {
        try {
            $roles = Role::with('permissions')->get();
            $permissions = Permission::orderBy('name')->get();
            $permissionsByCategory = $this->groupPermissionsByCategory($permissions);
            
            return view('admin.permissions.index', [
                'title' => 'Gestión de Permisos',
                'roles' => $roles,
                'permissions' => $permissions,
                'permissionsByCategory' => $permissionsByCategory
            ]);
            
        } catch (Exception $e) {
            return view('admin.permissions.index', [
                'title' => 'Gestión de Permisos',
                'roles' => [],
                'permissions' => [],
                'permissionsByCategory' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mostrar permisos de un rol específico (AJAX)
     */
    public function rolePermissions(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $roleId = $request->input('role_id');
            $role = Role::with('permissions')->find($roleId);
            
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            $allPermissions = Permission::orderBy('name')->get();
            
            return response()->json([
                'success' => true,
                'role' => [
                    'id' => $role->id,
                    'nombre' => $role->nombre,
                    'descripcion' => $role->descripcion
                ],
                'permissions' => $role->permissions->pluck('id')->toArray(),
                'all_permissions' => $allPermissions->map(function($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'descripcion' => $permission->descripcion ?? ''
                    ];
                })
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener permisos del rol: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar permisos a un rol (AJAX)
     */
    public function assignPermissions(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $validator = Validator::make($request->all(), [
                'role_id' => 'required|exists:roles,id',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $role = Role::findOrFail($request->role_id);
            $permissionIds = $request->input('permissions', []);

            // Obtener permisos anteriores para logging
            $oldPermissions = $role->permissions->pluck('name')->toArray();
            
            // Sincronizar permisos
            $role->permissions()->sync($permissionIds);
            
            // Obtener nuevos permisos para logging
            $role->load('permissions');
            $newPermissions = $role->permissions->pluck('name')->toArray();

            // Log del cambio
            Log::info('Permisos actualizados', [
                'role_id' => $role->id,
                'role_name' => $role->nombre,
                'old_permissions' => $oldPermissions,
                'new_permissions' => $newPermissions,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permisos actualizados exitosamente',
                'permissions_count' => count($permissionIds)
            ]);

        } catch (Exception $e) {
            Log::error('Error asignando permisos', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Ver permisos de un usuario específico (AJAX)
     */
    public function userPermissions(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $userId = $request->input('user_id');
            $user = User::with(['roles.permissions'])->find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Recopilar todos los permisos del usuario a través de sus roles
            $userPermissions = [];
            foreach ($user->roles as $role) {
                foreach ($role->permissions as $permission) {
                    if (!isset($userPermissions[$permission->name])) {
                        $userPermissions[$permission->name] = [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'descripcion' => $permission->descripcion ?? '',
                            'via_roles' => []
                        ];
                    }
                    $userPermissions[$permission->name]['via_roles'][] = $role->nombre;
                }
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->nombre . ' ' . $user->apellido,
                    'email' => $user->email
                ],
                'roles' => $user->roles->map(function($role) {
                    return [
                        'id' => $role->id,
                        'nombre' => $role->nombre,
                        'descripcion' => $role->descripcion
                    ];
                }),
                'permissions' => array_values($userPermissions),
                'permissions_count' => count($userPermissions)
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener permisos del usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo permiso (AJAX)
     */
    public function createPermission(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:permissions,name',
                'descripcion' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $permission = Permission::create([
                'name' => $request->input('name'),
                'guard_name' => 'web',
                'descripcion' => $request->input('descripcion', '')
            ]);

            // Log de la creación
            Log::info('Permiso creado', [
                'permission_id' => $permission->id,
                'permission_name' => $permission->name,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso creado exitosamente',
                'permission' => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'descripcion' => $permission->descripcion,
                    'guard_name' => $permission->guard_name
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error creando permiso', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Crear nuevo rol (AJAX)
     */
    public function createRole(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255|unique:roles,nombre',
                'descripcion' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $role = Role::create([
                'nombre' => $request->input('nombre'),
                'guard_name' => 'web',
                'descripcion' => $request->input('descripcion', '')
            ]);

            // Log de la creación
            Log::info('Rol creado', [
                'role_id' => $role->id,
                'role_name' => $role->nombre,
                'created_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rol creado exitosamente',
                'role' => [
                    'id' => $role->id,
                    'nombre' => $role->nombre,
                    'descripcion' => $role->descripcion,
                    'guard_name' => $role->guard_name
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error creando rol', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Matriz de permisos por rol
     */
    public function permissionMatrix(): View
    {
        try {
            $roles = Role::with('permissions')->get();
            $permissions = Permission::orderBy('name')->get();
            $permissionsByCategory = $this->groupPermissionsByCategory($permissions);
            
            // Construir matriz de permisos
            $matrix = [];
            foreach ($roles as $role) {
                $rolePermissions = $role->permissions->pluck('name')->toArray();
                
                $matrix[$role->nombre] = [];
                foreach ($permissions as $permission) {
                    $matrix[$role->nombre][$permission->name] = in_array($permission->name, $rolePermissions);
                }
            }

            return view('admin.permissions.matrix', [
                'title' => 'Matriz de Permisos',
                'roles' => $roles,
                'permissions' => $permissions,
                'permissionsByCategory' => $permissionsByCategory,
                'matrix' => $matrix
            ]);
            
        } catch (Exception $e) {
            return view('admin.permissions.matrix', [
                'title' => 'Matriz de Permisos',
                'roles' => [],
                'permissions' => [],
                'permissionsByCategory' => [],
                'matrix' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Exportar configuración de permisos
     */
    public function exportPermissions()
    {
        try {
            $roles = Role::with('permissions')->get();
            $permissions = Permission::all();
            $matrix = [];

            foreach ($roles as $role) {
                $matrix[$role->nombre] = $role->permissions->pluck('name')->toArray();
            }

            $export = [
                'exported_at' => now()->toDateTimeString(),
                'exported_by' => Auth::user()->email ?? 'system',
                'roles' => $roles->map(function($role) {
                    return [
                        'id' => $role->id,
                        'nombre' => $role->nombre,
                        'descripcion' => $role->descripcion,
                        'guard_name' => $role->guard_name
                    ];
                }),
                'permissions' => $permissions->map(function($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'descripcion' => $permission->descripcion,
                        'guard_name' => $permission->guard_name
                    ];
                }),
                'role_permissions' => $matrix
            ];

            $filename = 'permissions_export_' . now()->format('Y-m-d_H-i-s') . '.json';
            
            return response()->json($export, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            Log::error('Error exportando permisos', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar permisos'
            ], 500);
        }
    }

    /**
     * Validar si un usuario puede acceder a una ruta específica (AJAX)
     */
    public function checkAccess(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:usuarios,id',
                'permission' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $user = User::find($request->user_id);
            $hasAccess = $user->hasPermission($request->permission);
            
            return response()->json([
                'success' => true,
                'has_access' => $hasAccess,
                'user_id' => $request->user_id,
                'permission' => $request->permission,
                'user_name' => $user->nombre . ' ' . $user->apellido
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar acceso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agrupar permisos por categoría
     */
    private function groupPermissionsByCategory($permissions): array
    {
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $category = $parts[0] ?? 'general';
            
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            
            $grouped[$category][] = $permission;
        }

        return $grouped;
    }
}