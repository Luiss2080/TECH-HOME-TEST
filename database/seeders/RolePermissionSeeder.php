<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asignar permisos a roles
        $role_permissions = [
            // Administrador (rol_id = 1) - Todos los permisos
            ['permission_id' => 1, 'role_id' => 1], // login
            ['permission_id' => 2, 'role_id' => 1], // logout
            ['permission_id' => 3, 'role_id' => 1], // admin.dashboard
            ['permission_id' => 4, 'role_id' => 1], // admin.reportes
            ['permission_id' => 5, 'role_id' => 1], // admin.configuracion
            ['permission_id' => 6, 'role_id' => 1], // admin.usuarios.ver
            ['permission_id' => 7, 'role_id' => 1], // admin.usuarios.crear
            ['permission_id' => 8, 'role_id' => 1], // admin.usuarios.editar
            ['permission_id' => 9, 'role_id' => 1], // admin.usuarios.eliminar
            ['permission_id' => 10, 'role_id' => 1], // admin.usuarios.roles
            ['permission_id' => 11, 'role_id' => 1], // admin.usuarios.permisos
            ['permission_id' => 12, 'role_id' => 1], // admin.ventas.ver
            ['permission_id' => 13, 'role_id' => 1], // admin.ventas.crear
            ['permission_id' => 14, 'role_id' => 1], // admin.ventas.editar
            ['permission_id' => 15, 'role_id' => 1], // admin.ventas.eliminar
            ['permission_id' => 16, 'role_id' => 1], // estudiantes.dashboard
            ['permission_id' => 17, 'role_id' => 1], // cursos.ver
            ['permission_id' => 18, 'role_id' => 1], // cursos.crear
            ['permission_id' => 19, 'role_id' => 1], // cursos.editar
            ['permission_id' => 20, 'role_id' => 1], // cursos.eliminar
            ['permission_id' => 21, 'role_id' => 1], // libros.ver
            ['permission_id' => 22, 'role_id' => 1], // libros.crear
            ['permission_id' => 23, 'role_id' => 1], // libros.editar
            ['permission_id' => 24, 'role_id' => 1], // libros.eliminar
            ['permission_id' => 25, 'role_id' => 1], // libros.descargar
            ['permission_id' => 26, 'role_id' => 1], // materiales.ver
            ['permission_id' => 27, 'role_id' => 1], // materiales.crear
            ['permission_id' => 28, 'role_id' => 1], // materiales.editar
            ['permission_id' => 29, 'role_id' => 1], // materiales.eliminar
            ['permission_id' => 30, 'role_id' => 1], // laboratorios.ver
            ['permission_id' => 31, 'role_id' => 1], // laboratorios.crear
            ['permission_id' => 32, 'role_id' => 1], // laboratorios.editar
            ['permission_id' => 33, 'role_id' => 1], // laboratorios.eliminar
            ['permission_id' => 34, 'role_id' => 1], // componentes.ver
            ['permission_id' => 35, 'role_id' => 1], // componentes.crear
            ['permission_id' => 36, 'role_id' => 1], // componentes.editar
            ['permission_id' => 37, 'role_id' => 1], // componentes.eliminar
            ['permission_id' => 38, 'role_id' => 1], // docente.dashboard
            ['permission_id' => 39, 'role_id' => 1], // api.verify_session

            // Docente (rol_id = 2) - Permisos de enseñanza
            ['permission_id' => 1, 'role_id' => 2], // login
            ['permission_id' => 2, 'role_id' => 2], // logout
            ['permission_id' => 17, 'role_id' => 2], // cursos.ver
            ['permission_id' => 18, 'role_id' => 2], // cursos.crear
            ['permission_id' => 19, 'role_id' => 2], // cursos.editar
            ['permission_id' => 21, 'role_id' => 2], // libros.ver
            ['permission_id' => 25, 'role_id' => 2], // libros.descargar
            ['permission_id' => 26, 'role_id' => 2], // materiales.ver
            ['permission_id' => 27, 'role_id' => 2], // materiales.crear
            ['permission_id' => 28, 'role_id' => 2], // materiales.editar
            ['permission_id' => 30, 'role_id' => 2], // laboratorios.ver
            ['permission_id' => 31, 'role_id' => 2], // laboratorios.crear
            ['permission_id' => 32, 'role_id' => 2], // laboratorios.editar
            ['permission_id' => 34, 'role_id' => 2], // componentes.ver
            ['permission_id' => 38, 'role_id' => 2], // docente.dashboard
            ['permission_id' => 39, 'role_id' => 2], // api.verify_session

            // Estudiante (rol_id = 3) - Permisos básicos
            ['permission_id' => 1, 'role_id' => 3], // login
            ['permission_id' => 2, 'role_id' => 3], // logout
            ['permission_id' => 16, 'role_id' => 3], // estudiantes.dashboard
            ['permission_id' => 17, 'role_id' => 3], // cursos.ver
            ['permission_id' => 21, 'role_id' => 3], // libros.ver
            ['permission_id' => 25, 'role_id' => 3], // libros.descargar
            ['permission_id' => 26, 'role_id' => 3], // materiales.ver
            ['permission_id' => 30, 'role_id' => 3], // laboratorios.ver
            ['permission_id' => 34, 'role_id' => 3], // componentes.ver
            ['permission_id' => 39, 'role_id' => 3], // api.verify_session

            // Invitado (rol_id = 4) - Permisos temporales limitados
            ['permission_id' => 1, 'role_id' => 4], // login
            ['permission_id' => 2, 'role_id' => 4], // logout
            ['permission_id' => 17, 'role_id' => 4], // cursos.ver
            ['permission_id' => 21, 'role_id' => 4], // libros.ver
            ['permission_id' => 25, 'role_id' => 4], // libros.descargar
            ['permission_id' => 26, 'role_id' => 4], // materiales.ver
            ['permission_id' => 30, 'role_id' => 4], // laboratorios.ver
            ['permission_id' => 39, 'role_id' => 4], // api.verify_session
        ];

        foreach ($role_permissions as $role_permission) {
            DB::table('role_has_permissions')->insert($role_permission);
        }

        // Asignar roles a usuarios
        $model_has_roles = [
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 1], // Admin
            ['role_id' => 2, 'model_type' => 'App\\Models\\User', 'model_id' => 2], // Docente  
            ['role_id' => 3, 'model_type' => 'App\\Models\\User', 'model_id' => 3], // Estudiante
        ];

        foreach ($model_has_roles as $model_role) {
            DB::table('model_has_roles')->insert($model_role);
        }
    }
}
