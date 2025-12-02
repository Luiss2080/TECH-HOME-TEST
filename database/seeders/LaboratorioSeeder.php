<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Laboratorio;

class LaboratorioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laboratorios = [
            [
                'nombre' => 'Laboratorio de Robótica Avanzada',
                'descripcion' => 'Laboratorio equipado con robots industriales y de servicio para prácticas avanzadas',
                'ubicacion' => 'Planta Baja - Ala Este',
                'capacidad' => 25,
                'equipamiento' => [
                    'Robots industriales KUKA',
                    'Brazos robóticos ABB',
                    'Sensores de visión artificial',
                    'Sistemas de control PLC',
                    'Estaciones de soldadura robotizada'
                ],
                'estado' => 'activo',
                'disponibilidad' => 'disponible',
                'responsable' => 'Dr. Carlos Mendoza',
                'horarios' => 'Lunes a Viernes: 08:00 - 18:00'
            ],
            [
                'nombre' => 'Laboratorio de Inteligencia Artificial',
                'descripcion' => 'Espacio dedicado al desarrollo y pruebas de algoritmos de IA y Machine Learning',
                'ubicacion' => 'Segundo Piso - Ala Norte',
                'capacidad' => 30,
                'equipamiento' => [
                    'Servidores con GPU Tesla V100',
                    'Estaciones de trabajo de alta gama',
                    'Cluster de computación paralela',
                    'Cámaras de visión computacional',
                    'Dispositivos IoT para pruebas'
                ],
                'estado' => 'activo',
                'disponibilidad' => 'disponible',
                'responsable' => 'Dra. Ana Gutiérrez',
                'horarios' => 'Lunes a Viernes: 07:00 - 20:00'
            ],
            [
                'nombre' => 'Laboratorio de Electrónica Digital',
                'descripcion' => 'Laboratorio para prácticas de electrónica, microcontroladores y sistemas embebidos',
                'ubicacion' => 'Planta Baja - Ala Oeste',
                'capacidad' => 20,
                'equipamiento' => [
                    'Arduino y Raspberry Pi',
                    'Osciloscopios digitales',
                    'Generadores de señales',
                    'Multímetros de precisión',
                    'Protoboards y componentes electrónicos'
                ],
                'estado' => 'activo',
                'disponibilidad' => 'ocupado',
                'responsable' => 'Ing. Roberto Silva',
                'horarios' => 'Lunes a Viernes: 08:00 - 17:00'
            ],
            [
                'nombre' => 'Laboratorio de Automatización Industrial',
                'descripcion' => 'Simulación de procesos industriales automatizados',
                'ubicacion' => 'Primer Piso - Central',
                'capacidad' => 18,
                'equipamiento' => [
                    'PLCs Siemens y Allen Bradley',
                    'HMI táctiles industriales',
                    'Variadores de frecuencia',
                    'Sensores industriales',
                    'Módulos de comunicación industrial'
                ],
                'estado' => 'activo',
                'disponibilidad' => 'reservado',
                'responsable' => 'Ing. María López',
                'horarios' => 'Lunes a Viernes: 09:00 - 16:00'
            ],
            [
                'nombre' => 'Laboratorio de Prototipado 3D',
                'descripcion' => 'Espacio para diseño e impresión 3D de prototipos robóticos',
                'ubicacion' => 'Segundo Piso - Ala Sur',
                'capacidad' => 15,
                'equipamiento' => [
                    'Impresoras 3D industriales',
                    'Scanner 3D de alta precisión',
                    'Software CAD especializado',
                    'Cortadora láser',
                    'Herramientas de post-procesado'
                ],
                'estado' => 'mantenimiento',
                'disponibilidad' => 'disponible',
                'responsable' => 'Ing. Pedro Vargas',
                'horarios' => 'Lunes a Viernes: 10:00 - 15:00'
            ]
        ];

        foreach ($laboratorios as $laboratorio) {
            Laboratorio::create($laboratorio);
        }
    }
}
