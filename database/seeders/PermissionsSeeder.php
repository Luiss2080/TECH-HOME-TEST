<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Sistema básico
            ['name' => 'login', 'guard_name' => 'web'],
            ['name' => 'logout', 'guard_name' => 'web'],
            
            // Admin Dashboard
            ['name' => 'admin.dashboard', 'guard_name' => 'web'],
            ['name' => 'admin.reportes', 'guard_name' => 'web'],
            ['name' => 'admin.configuracion', 'guard_name' => 'web'],
            
            // Gestión de usuarios
            ['name' => 'admin.usuarios.ver', 'guard_name' => 'web'],
            ['name' => 'admin.usuarios.crear', 'guard_name' => 'web'],
            ['name' => 'admin.usuarios.editar', 'guard_name' => 'web'],
            ['name' => 'admin.usuarios.eliminar', 'guard_name' => 'web'],
            ['name' => 'admin.usuarios.roles', 'guard_name' => 'web'],
            ['name' => 'admin.usuarios.permisos', 'guard_name' => 'web'],
            
            // Gestión de ventas
            ['name' => 'admin.ventas.ver', 'guard_name' => 'web'],
            ['name' => 'admin.ventas.crear', 'guard_name' => 'web'],
            ['name' => 'admin.ventas.editar', 'guard_name' => 'web'],
            ['name' => 'admin.ventas.eliminar', 'guard_name' => 'web'],
            
            // Dashboard estudiante
            ['name' => 'estudiantes.dashboard', 'guard_name' => 'web'],
            
            // Gestión de cursos
            ['name' => 'cursos.ver', 'guard_name' => 'web'],
            ['name' => 'cursos.crear', 'guard_name' => 'web'],
            ['name' => 'cursos.editar', 'guard_name' => 'web'],
            ['name' => 'cursos.eliminar', 'guard_name' => 'web'],
            
            // Gestión de libros
            ['name' => 'libros.ver', 'guard_name' => 'web'],
            ['name' => 'libros.crear', 'guard_name' => 'web'],
            ['name' => 'libros.editar', 'guard_name' => 'web'],
            ['name' => 'libros.eliminar', 'guard_name' => 'web'],
            ['name' => 'libros.descargar', 'guard_name' => 'web'],
            
            // Gestión de materiales
            ['name' => 'materiales.ver', 'guard_name' => 'web'],
            ['name' => 'materiales.crear', 'guard_name' => 'web'],
            ['name' => 'materiales.editar', 'guard_name' => 'web'],
            ['name' => 'materiales.eliminar', 'guard_name' => 'web'],
            
            // Gestión de laboratorios
            ['name' => 'laboratorios.ver', 'guard_name' => 'web'],
            ['name' => 'laboratorios.crear', 'guard_name' => 'web'],
            ['name' => 'laboratorios.editar', 'guard_name' => 'web'],
            ['name' => 'laboratorios.eliminar', 'guard_name' => 'web'],
            
            // Gestión de componentes
            ['name' => 'componentes.ver', 'guard_name' => 'web'],
            ['name' => 'componentes.crear', 'guard_name' => 'web'],
            ['name' => 'componentes.editar', 'guard_name' => 'web'],
            ['name' => 'componentes.eliminar', 'guard_name' => 'web'],
            
            // Dashboard docente
            ['name' => 'docente.dashboard', 'guard_name' => 'web'],
            
            // API
            ['name' => 'api.verify_session', 'guard_name' => 'web'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}