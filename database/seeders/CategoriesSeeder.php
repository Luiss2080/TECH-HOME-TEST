<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                'activa' => true,
            ],
            [
                'nombre' => 'Base de Datos',
                'descripcion' => 'Cursos sobre diseño y administración de bases de datos',
                'activa' => true,
            ],
            [
                'nombre' => 'Redes y Seguridad',
                'descripcion' => 'Cursos de redes de computadoras y ciberseguridad',
                'activa' => true,
            ],
            [
                'nombre' => 'Inteligencia Artificial',
                'descripcion' => 'Cursos sobre IA, Machine Learning y Data Science',
                'activa' => true,
            ],
            [
                'nombre' => 'Desarrollo Móvil',
                'descripcion' => 'Cursos de desarrollo de aplicaciones móviles',
                'activa' => true,
            ],
        ];

        foreach ($categories as $category) {
            \DB::table('categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
