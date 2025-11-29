<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materiales = [
            [
                'titulo' => 'Manual Completo de Arduino',
                'descripcion' => 'Guía detallada para programar microcontroladores Arduino',
                'tipo' => 'pdf',
                'archivo' => '/materiales/arduino/manual_arduino_completo.pdf',
                'tamaño_archivo' => 3072, // KB
                'duracion' => null,
                'categoria_id' => 1, // Programación Web
                'docente_id' => 2,
                'imagen_preview' => '/materiales/img/arduino_manual.jpg',
                'publico' => true,
                'descargas' => 245,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Tutorial: Sensores con Raspberry Pi',
                'descripcion' => 'Configuración y uso de sensores con Raspberry Pi',
                'tipo' => 'video',
                'enlace_externo' => 'https://youtu.be/sensor_rpi_tutorial',
                'duracion' => 1800, // 30 minutos en segundos
                'categoria_id' => 4, // Inteligencia Artificial
                'docente_id' => 2,
                'imagen_preview' => '/materiales/img/rpi_sensores.jpg',
                'publico' => true,
                'descargas' => 0,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Código: Proyecto Robot Seguidor',
                'descripcion' => 'Código completo para robot que sigue líneas',
                'tipo' => 'codigo',
                'archivo' => '/materiales/robotica/robot_seguidor.ino',
                'tamaño_archivo' => 512,
                'categoria_id' => 5, // Desarrollo Móvil (robótica)
                'docente_id' => 2,
                'imagen_preview' => '/materiales/img/robot_seguidor.jpg',
                'publico' => false,
                'descargas' => 89,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Esquemas Electrónicos Básicos',
                'descripcion' => 'Diagramas de circuitos electrónicos fundamentales',
                'tipo' => 'imagen',
                'archivo' => '/materiales/electronica/esquemas_basicos.zip',
                'tamaño_archivo' => 1024,
                'categoria_id' => 1,
                'docente_id' => 2,
                'imagen_preview' => '/materiales/img/esquemas.jpg',
                'publico' => true,
                'descargas' => 156,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Dataset: Lecturas Ultrasonido',
                'descripcion' => 'CSV con distancias medidas en diferentes escenarios',
                'tipo' => 'dataset',
                'archivo' => '/materiales/robotica/dataset_ultrasonido.csv',
                'tamaño_archivo' => 2560,
                'categoria_id' => 4,
                'docente_id' => 2,
                'imagen_preview' => '/materiales/img/dataset_ultra.jpg',
                'publico' => false,
                'descargas' => 67,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Apuntes de POO en Java',
                'descripcion' => 'Colecciones, genéricos y patrones básicos',
                'tipo' => 'pdf',
                'archivo' => '/materiales/programacion/poo_java.pdf',
                'tamaño_arquivo' => 1536,
                'categoria_id' => 1,
                'docente_id' => 2,
                'imagen_preview' => '/materiales/img/poo_java.jpg',
                'publico' => true,
                'descargas' => 89,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Simulador de Circuitos Online',
                'descripcion' => 'Herramienta web para simular circuitos electrónicos',
                'tipo' => 'herramienta',
                'enlace_externo' => 'https://www.falstad.com/circuit/',
                'categoria_id' => 1,
                'docente_id' => 2,
                'imagen_preview' => '/materiales/img/simulador.jpg',
                'publico' => true,
                'descargas' => 0,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Guía: Configuración Raspberry Pi OS',
                'descripcion' => 'Instalación y configuración inicial del sistema operativo',
                'tipo' => 'pdf',
                'archivo' => '/materiales/raspberry/guia_instalacion.pdf',
                'tamaño_archivo' => 2048,
                'categoria_id' => 3, // Redes y Seguridad
                'docente_id' => 2,
                'imagen_preview' => '/materiales/img/rpi_os.jpg',
                'publico' => true,
                'descargas' => 123,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Video: Introducción a IoT',
                'descripcion' => 'Conceptos básicos del Internet de las Cosas',
                'tipo' => 'video',
                'enlace_externo' => 'https://youtu.be/iot_intro_2024',
                'duracion' => 2400, // 40 minutos
                'categoria_id' => 3,
                'docente_id' => 2,
                'imagen_preview' => '/materiales/img/iot_intro.jpg',
                'publico' => true,
                'descargas' => 0,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Librería: Funciones Matemáticas',
                'descripcion' => 'Colección de funciones matemáticas para Arduino',
                'tipo' => 'codigo',
                'archivo' => '/materiales/librerias/math_functions.h',
                'tamaño_archivo' => 256,
                'categoria_id' => 4,
                'docente_id' => 2,
                'imagen_preview' => '/materiales/img/math_lib.jpg',
                'publico' => false,
                'descargas' => 45,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($materiales as $material) {
            DB::table('materials')->insert($material);
        }
    }
}