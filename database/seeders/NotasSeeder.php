<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notas = [
            [
                'estudiante_id' => 3, // Usuario estudiante
                'curso_id' => 1, // Programación Web Básica
                'nota' => 85.50,
                'fecha_calificacion' => now()->subDays(10),
            ],
            [
                'estudiante_id' => 3,
                'curso_id' => 2, // Base de Datos MySQL
                'nota' => 92.75,
                'fecha_calificacion' => now()->subDays(5),
            ],
            [
                'estudiante_id' => 3,
                'curso_id' => 5, // Desarrollo Android (completado)
                'nota' => 96.00,
                'fecha_calificacion' => now()->subDays(3),
            ],
        ];

        foreach ($notas as $nota) {
            DB::table('notas')->insert($nota);
        }
    }
}
