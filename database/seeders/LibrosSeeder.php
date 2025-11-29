<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LibrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $libros = [
            [
                'titulo' => 'HTML5 y CSS3 para Principiantes',
                'autor' => 'Juan Pérez',
                'descripcion' => 'Guía completa para aprender desarrollo web frontend desde cero',
                'categoria_id' => 1, // Programación Web
                'isbn' => '978-84-415-3949-1',
                'paginas' => 320,
                'editorial' => 'Tech Books',
                'año_publicacion' => 2024,
                'imagen_portada' => '/img/libros/html5_css3.jpg',
                'archivo_pdf' => '/libros/html5_css3_principiantes.pdf',
                'tamaño_archivo' => 15360, // KB
                'stock' => 50,
                'stock_minimo' => 10,
                'precio' => 45.00,
                'es_gratuito' => false,
                'estado' => true,
                
                
            ],
            [
                'titulo' => 'Bases de Datos Relacionales',
                'autor' => 'María González',
                'descripcion' => 'Diseño y implementación de bases de datos relacionales',
                'categoria_id' => 2, // Base de Datos
                'isbn' => '978-84-415-3950-7',
                'paginas' => 450,
                'editorial' => 'Data Publications',
                'año_publicacion' => 2024,
                'imagen_portada' => '/img/libros/bd_relacionales.jpg',
                'archivo_pdf' => '/libros/bases_datos_relacionales.pdf',
                'tamaño_archivo' => 22500,
                'stock' => 30,
                'stock_minimo' => 5,
                'precio' => 65.00,
                'es_gratuito' => false,
                'estado' => true,
                
                
            ],
            [
                'titulo' => 'Ciberseguridad Práctica',
                'autor' => 'Carlos Rodríguez',
                'descripcion' => 'Manual práctico de seguridad informática y protección de datos',
                'categoria_id' => 3, // Redes y Seguridad
                'isbn' => '978-84-415-3951-4',
                'paginas' => 380,
                'editorial' => 'Security Press',
                'año_publicacion' => 2024,
                'imagen_portada' => '/img/libros/ciberseguridad.jpg',
                'archivo_pdf' => '/libros/ciberseguridad_practica.pdf',
                'tamaño_archivo' => 18900,
                'stock' => 25,
                'stock_minimo' => 8,
                'precio' => 75.00,
                'es_gratuito' => false,
                'estado' => true,
                
                
            ],
            [
                'titulo' => 'Machine Learning con Python',
                'autor' => 'Ana Martínez',
                'descripcion' => 'Implementación práctica de algoritmos de aprendizaje automático',
                'categoria_id' => 4, // Inteligencia Artificial
                'isbn' => '978-84-415-3952-1',
                'paginas' => 520,
                'editorial' => 'AI Books',
                'año_publicacion' => 2024,
                'imagen_portada' => '/img/libros/ml_python.jpg',
                'archivo_pdf' => '/libros/machine_learning_python.pdf',
                'tamaño_archivo' => 28700,
                'stock' => 40,
                'stock_minimo' => 12,
                'precio' => 85.00,
                'es_gratuito' => false,
                'estado' => true,
                
                
            ],
            [
                'titulo' => 'Desarrollo de Apps Android',
                'autor' => 'Luis Fernández',
                'descripcion' => 'Guía completa para crear aplicaciones móviles nativas',
                'categoria_id' => 5, // Desarrollo Móvil
                'isbn' => '978-84-415-3953-8',
                'paginas' => 420,
                'editorial' => 'Mobile Dev',
                'año_publicacion' => 2024,
                'imagen_portada' => '/img/libros/android_dev.jpg',
                'archivo_pdf' => '/libros/desarrollo_apps_android.pdf',
                'tamaño_archivo' => 25600,
                'stock' => 35,
                'stock_minimo' => 10,
                'precio' => 70.00,
                'es_gratuito' => false,
                'estado' => true,
                
                
            ],
            [
                'titulo' => 'Introducción a la Programación',
                'autor' => 'Open Source Community',
                'descripcion' => 'Libro gratuito con conceptos básicos de programación',
                'categoria_id' => 1, // Programación Web
                'isbn' => '978-84-415-3954-5',
                'paginas' => 280,
                'editorial' => 'Open Books',
                'año_publicacion' => 2024,
                'imagen_portada' => '/img/libros/intro_programacion.jpg',
                'archivo_pdf' => '/libros/introduccion_programacion.pdf',
                'tamaño_archivo' => 12800,
                'stock' => 0,
                'stock_minimo' => 0,
                'precio' => 0.00,
                'es_gratuito' => true,
                'estado' => true,
                
                
            ],
        ];

        foreach ($libros as $libro) {
            DB::table('books')->insert($libro);
        }
    }
}
