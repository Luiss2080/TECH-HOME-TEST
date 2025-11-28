<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cursos = [
            [
                'titulo' => 'Programación Web Básica',
                'descripcion' => 'Aprende los fundamentos de HTML, CSS y JavaScript',
                'contenido' => 'Curso completo que cubre desde etiquetas HTML básicas hasta JavaScript interactivo',
                'docente_id' => 2, // Docente creado en UsersSeeder
                'categoria_id' => 1, // Programación Web
                'imagen_portada' => '/img/cursos/web_basico.jpg',
                'precio' => 0.00,
                'duracion_horas' => 40,
                'nivel' => 'Principiante',
                'requisitos' => 'Conocimientos básicos de computación',
                'objetivos' => 'Dominar los fundamentos del desarrollo web frontend',
                'estado' => 'Publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Base de Datos MySQL',
                'descripcion' => 'Diseño y administración de bases de datos relacionales',
                'contenido' => 'Desde conceptos básicos hasta consultas complejas y optimización',
                'docente_id' => 2,
                'categoria_id' => 2, // Base de Datos
                'imagen_portada' => '/img/cursos/mysql.jpg',
                'precio' => 149.00,
                'duracion_horas' => 35,
                'nivel' => 'Intermedio',
                'requisitos' => 'Conceptos básicos de programación',
                'objetivos' => 'Diseñar y gestionar bases de datos eficientemente',
                'estado' => 'Publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Seguridad en Redes',
                'descripcion' => 'Fundamentos de ciberseguridad y protección de redes',
                'contenido' => 'Protocolos de seguridad, firewalls, detección de intrusos',
                'docente_id' => 2,
                'categoria_id' => 3, // Redes y Seguridad
                'imagen_portada' => '/img/cursos/seguridad.jpg',
                'precio' => 199.00,
                'duracion_horas' => 50,
                'nivel' => 'Avanzado',
                'requisitos' => 'Conocimientos de redes de computadoras',
                'objetivos' => 'Implementar medidas de seguridad en infraestructuras de red',
                'estado' => 'Publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Introducción a Machine Learning',
                'descripcion' => 'Algoritmos de aprendizaje automático con Python',
                'contenido' => 'Algoritmos supervisados y no supervisados, evaluación de modelos',
                'docente_id' => 2,
                'categoria_id' => 4, // Inteligencia Artificial
                'imagen_portada' => '/img/cursos/ml.jpg',
                'precio' => 299.00,
                'duracion_horas' => 60,
                'nivel' => 'Intermedio',
                'requisitos' => 'Python básico, matemáticas estadísticas',
                'objetivos' => 'Crear y evaluar modelos de machine learning',
                'estado' => 'Publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Desarrollo Android con Kotlin',
                'descripcion' => 'Crear aplicaciones móviles nativas para Android',
                'contenido' => 'Desde la configuración del entorno hasta publicación en Play Store',
                'docente_id' => 2,
                'categoria_id' => 5, // Desarrollo Móvil
                'imagen_portada' => '/img/cursos/android.jpg',
                'precio' => 249.00,
                'duracion_horas' => 45,
                'nivel' => 'Intermedio',
                'requisitos' => 'Conocimientos de programación orientada a objetos',
                'objetivos' => 'Desarrollar aplicaciones móviles completas para Android',
                'estado' => 'Publicado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($cursos as $curso) {
            DB::table('courses')->insert($curso);
        }
    }
}