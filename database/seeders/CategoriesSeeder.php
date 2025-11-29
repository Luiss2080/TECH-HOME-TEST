<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nombre' => 'Programación Web',
                'descripcion' => 'Cursos relacionados con desarrollo web frontend y backend',
                'estado' => true,
            ],
            [
                'nombre' => 'Base de Datos',
                'descripcion' => 'Cursos sobre diseño y administración de bases de datos',
                'estado' => true,
            ],
            [
                'nombre' => 'Redes y Seguridad',
                'descripcion' => 'Cursos de redes de computadoras y ciberseguridad',
                'estado' => true,
            ],
            [
                'nombre' => 'Inteligencia Artificial',
                'descripcion' => 'Cursos sobre IA, Machine Learning y Data Science',
                'estado' => true,
            ],
            [
                'nombre' => 'Desarrollo Móvil',
                'descripcion' => 'Cursos de desarrollo de aplicaciones móviles',
                'estado' => true,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert(array_merge($category, [
                
                
            ]));
        }
    }
}
