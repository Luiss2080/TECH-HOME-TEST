<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InscripcionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inscripciones = [
            [
                'estudiante_id' => 3, // Usuario estudiante
                'curso_id' => 1, // Programación Web Básica
                'fecha_inscripcion' => now()->subDays(30),
                'estado' => 'activa',
                'precio_pagado' => 0.00, // Gratuito
                'notas' => 'Inscripción automática por ser gratuito',
            ],
            [
                'estudiante_id' => 3,
                'curso_id' => 2, // Base de Datos MySQL
                'fecha_inscripcion' => now()->subDays(25),
                'estado' => 'activa',
                'precio_pagado' => 149.00,
                'notas' => 'Pagado con tarjeta de crédito',
            ],
            [
                'estudiante_id' => 3,
                'curso_id' => 5, // Desarrollo Android
                'fecha_inscripcion' => now()->subDays(20),
                'estado' => 'completada',
                'precio_pagado' => 249.00,
                'notas' => 'Curso completado exitosamente - Certificado emitido',
            ],
        ];

        foreach ($inscripciones as $inscripcion) {
            DB::table('inscripciones')->insert($inscripcion);
        }

        // Ahora crear progreso de estudiantes
        $progresos = [
            [
                'estudiante_id' => 3,
                'curso_id' => 1,
                'progreso_porcentaje' => 75.50,
                'tiempo_estudiado' => 28800, // 8 horas en segundos
                'ultima_actividad' => now()->subHours(2),
                'completado' => false,
                'fecha_inscripcion' => now()->subDays(30),
            ],
            [
                'estudiante_id' => 3,
                'curso_id' => 2,
                'progreso_porcentaje' => 45.30,
                'tiempo_estudiado' => 18000, // 5 horas en segundos
                'ultima_actividad' => now()->subDays(1),
                'completado' => false,
                'fecha_inscripcion' => now()->subDays(25),
            ],
            [
                'estudiante_id' => 3,
                'curso_id' => 5,
                'progreso_porcentaje' => 100.00,
                'tiempo_estudiado' => 162000, // 45 horas en segundos
                'ultima_actividad' => now()->subDays(5),
                'completado' => true,
                'fecha_inscripcion' => now()->subDays(20),
            ],
        ];

        foreach ($progresos as $progreso) {
            DB::table('progreso_estudiantes')->insert($progreso);
        }
    }
}
