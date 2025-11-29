<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso completo al sistema',
                'estado' => true,
            ],
            [
                'nombre' => 'Docente',
                'descripcion' => 'Puede crear y gestionar cursos',
                'estado' => true,
            ],
            [
                'nombre' => 'Estudiante',
                'descripcion' => 'Puede acceder a cursos y materiales',
                'estado' => true,
            ],
            [
                'nombre' => 'Invitado',
                'descripcion' => 'Acceso temporal de 3 días a todo el material',
                'estado' => true,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }
    }
}
