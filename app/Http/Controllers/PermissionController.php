<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class PermissionController extends Controller
{
    /**
     * Panel principal de gestión de permisos
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                return back()->withErrors(['error' => 'No tiene permisos para acceder a esta sección.']);
            }

            $roles = Role::with(['permissions'])->orderBy('nombre')->get();
            $permissions = Permission::orderBy('categoria')->orderBy('nombre')->get();
            
            // Agrupar permisos por categoría
            $permissionsByCategory = $permissions->groupBy('categoria');

            return view('admin.permissions.index', compact('roles', 'permissions', 'permissionsByCategory'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar permisos: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtener permisos de un rol específico (AJAX)
     */
    public function rolePermissions(Request $request, $roleId)
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
            }

            $role = Role::with('permissions')->findOrFail($roleId);
            $allPermissions = Permission::orderBy('categoria')->orderBy('nombre')->get();
            
            $rolePermissionIds = $role->permissions->pluck('id')->toArray();

            return response()->json([
                'success' => true,
                'role' => $role,
                'permissions' => $allPermissions,
                'rolePermissions' => $rolePermissionIds
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar permisos del rol: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar permisos de un rol
     */
    public function updateRolePermissions(Request $request, $roleId)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator);
        }

        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
                return back()->withErrors(['error' => 'No tiene permisos para realizar esta acción.']);
            }

            DB::beginTransaction();

            $role = Role::findOrFail($roleId);
            
            // Obtener permisos actuales y nuevos
            $currentPermissions = $role->permissions()->pluck('permissions.id')->toArray();
            $newPermissions = $request->get('permissions', []);

            // Sincronizar permisos
            $role->permissions()->sync($newPermissions);

            // Registrar cambios en auditoría (si se implementa)
            // AuditLog::log('role_permissions_updated', $role->id, [
            //     'previous' => $currentPermissions,
            //     'new' => $newPermissions,
            //     'user_id' => $user->id
            // ]);

            DB::commit();

            $message = 'Permisos del rol actualizados exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            
            $error = 'Error al actualizar permisos: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error]);
        }
    }

    /**
     * Crear nuevo rol
     */
    public function createRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio',
            'nombre.unique' => 'Ya existe un rol con este nombre'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
                return back()->withErrors(['error' => 'No tiene permisos para crear roles.']);
            }

            DB::beginTransaction();

            $role = Role::create([
                'nombre' => strtolower($request->nombre),
                'descripcion' => $request->descripcion,
                'creado_en' => now()
            ]);

            // Asignar permisos si se proporcionan
            if ($request->filled('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            DB::commit();

            $message = 'Rol creado exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'role' => $role
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            
            $error = 'Error al crear rol: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error])->withInput();
        }
    }

    /**
     * Actualizar rol existente
     */
    public function updateRole(Request $request, $roleId)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:roles,nombre,' . $roleId,
            'descripcion' => 'nullable|string|max:500'
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio',
            'nombre.unique' => 'Ya existe un rol con este nombre'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
                return back()->withErrors(['error' => 'No tiene permisos para editar roles.']);
            }

            $role = Role::findOrFail($roleId);
            
            // No permitir editar roles sistema básicos
            if (in_array($role->nombre, ['administrador', 'docente', 'estudiante'])) {
                $error = 'No se puede modificar este rol del sistema.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 400);
                }
                return back()->withErrors(['error' => $error]);
            }

            $role->update([
                'nombre' => strtolower($request->nombre),
                'descripcion' => $request->descripcion,
                'actualizado_en' => now()
            ]);

            $message = 'Rol actualizado exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            $error = 'Error al actualizar rol: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error])->withInput();
        }
    }

    /**
     * Eliminar rol
     */
    public function deleteRole(Request $request, $roleId)
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
                return back()->withErrors(['error' => 'No tiene permisos para eliminar roles.']);
            }

            $role = Role::findOrFail($roleId);
            
            // No permitir eliminar roles sistema básicos
            if (in_array($role->nombre, ['administrador', 'docente', 'estudiante'])) {
                $error = 'No se puede eliminar este rol del sistema.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 400);
                }
                return back()->withErrors(['error' => $error]);
            }

            // Verificar si hay usuarios con este rol
            $usersCount = $role->users()->count();
            if ($usersCount > 0) {
                $error = "No se puede eliminar el rol porque tiene {$usersCount} usuario(s) asignado(s).";
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 400);
                }
                return back()->withErrors(['error' => $error]);
            }

            DB::beginTransaction();
            
            // Eliminar relaciones con permisos
            $role->permissions()->detach();
            
            // Eliminar rol
            $role->delete();

            DB::commit();

            $message = 'Rol eliminado exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            
            $error = 'Error al eliminar rol: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error]);
        }
    }

    /**
     * Gestionar permisos de usuario específico
     */
    public function userPermissions($userId)
    {
        try {
            $currentUser = Auth::user();
            
            if (!$currentUser->hasRole('administrador')) {
                return back()->withErrors(['error' => 'No tiene permisos para acceder a esta sección.']);
            }

            $user = User::with(['roles.permissions'])->findOrFail($userId);
            $allRoles = Role::with('permissions')->orderBy('nombre')->get();

            return view('admin.permissions.user', compact('user', 'allRoles'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar permisos de usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar roles de usuario
     */
    public function updateUserRoles(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator);
        }

        try {
            $currentUser = Auth::user();
            
            if (!$currentUser->hasRole('administrador')) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
                return back()->withErrors(['error' => 'No tiene permisos para realizar esta acción.']);
            }

            $user = User::findOrFail($userId);
            
            // No permitir modificar roles del propio usuario administrador
            if ($user->id === $currentUser->id) {
                $error = 'No puede modificar sus propios roles.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 400);
                }
                return back()->withErrors(['error' => $error]);
            }

            DB::beginTransaction();

            // Sincronizar roles
            $roles = $request->get('roles', []);
            $user->roles()->sync($roles);

            DB::commit();

            $message = 'Roles de usuario actualizados exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            
            $error = 'Error al actualizar roles: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error]);
        }
    }
}
