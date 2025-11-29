<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComponentesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $componentes = [
            [
                'nombre' => 'Arduino UNO R3',
                'descripcion' => 'Placa de desarrollo con microcontrolador ATmega328P',
                'categoria_id' => 1, // Programación Web (usamos las categorías existentes)
                'codigo_producto' => 'ARD-UNO-R3',
                'marca' => 'Arduino',
                'modelo' => 'UNO R3',
                'especificaciones' => json_encode([
                    'microcontrolador' => 'ATmega328P',
                    'voltaje_operacion' => '5V',
                    'pines_digitales' => 14,
                    'pines_analogos' => 6,
                    'memoria_flash' => '32KB'
                ]),
                'precio' => 45.00,
                'stock' => 50,
                'stock_minimo' => 10,
                'proveedor' => 'Arduino Store',
                'estado' => 'Disponible',
                
                
            ],
            [
                'nombre' => 'Raspberry Pi 4 Model B',
                'descripcion' => 'Computadora de placa única de 4GB RAM',
                'categoria_id' => 1,
                'codigo_producto' => 'RPI-4B-4GB',
                'marca' => 'Raspberry Pi',
                'modelo' => '4 Model B',
                'especificaciones' => json_encode([
                    'ram' => '4GB',
                    'cpu' => 'Quad-core ARM Cortex-A72',
                    'conectividad' => ['WiFi', 'Bluetooth', 'Ethernet'],
                    'puertos' => ['USB 3.0', 'USB 2.0', 'HDMI']
                ]),
                'precio' => 120.00,
                'stock' => 25,
                'stock_minimo' => 5,
                'proveedor' => 'Raspberry Foundation',
                'estado' => 'Disponible',
                
                
            ],
            [
                'nombre' => 'ESP32 DevKit V1',
                'descripcion' => 'Módulo WiFi y Bluetooth con microcontrolador dual-core',
                'categoria_id' => 3, // Redes y Seguridad
                'codigo_producto' => 'ESP32-DEVKIT',
                'marca' => 'Espressif',
                'modelo' => 'DevKit V1',
                'especificaciones' => json_encode([
                    'cpu' => 'Dual-core Tensilica Xtensa LX6',
                    'conectividad' => ['WiFi 802.11 b/g/n', 'Bluetooth 4.2'],
                    'memoria' => '520 KB SRAM',
                    'voltaje' => '3.3V'
                ]),
                'precio' => 25.00,
                'stock' => 75,
                'stock_minimo' => 15,
                'proveedor' => 'Espressif Systems',
                'estado' => 'Disponible',
                
                
            ],
            [
                'nombre' => 'Sensor Ultrasónico HC-SR04',
                'descripcion' => 'Sensor de distancia por ultrasonido',
                'categoria_id' => 4, // Inteligencia Artificial
                'codigo_producto' => 'HC-SR04',
                'marca' => 'Generic',
                'modelo' => 'HC-SR04',
                'especificaciones' => json_encode([
                    'rango_deteccion' => '2cm - 400cm',
                    'precision' => '3mm',
                    'voltaje' => '5V',
                    'frecuencia' => '40KHz'
                ]),
                'precio' => 8.00,
                'stock' => 100,
                'stock_minimo' => 20,
                'proveedor' => 'Electronics Pro',
                'estado' => 'Disponible',
                
                
            ],
            [
                'nombre' => 'Sensor de Temperatura DHT22',
                'descripcion' => 'Sensor digital de temperatura y humedad',
                'categoria_id' => 4, // Inteligencia Artificial
                'codigo_producto' => 'DHT22',
                'marca' => 'Aosong',
                'modelo' => 'DHT22',
                'especificaciones' => json_encode([
                    'rango_temperatura' => '-40°C a 80°C',
                    'rango_humedad' => '0% a 100% RH',
                    'precision_temp' => '±0.5°C',
                    'precision_humedad' => '±2% RH'
                ]),
                'precio' => 12.00,
                'stock' => 80,
                'stock_minimo' => 15,
                'proveedor' => 'Sensor Tech',
                'estado' => 'Disponible',
                
                
            ],
            [
                'nombre' => 'Servo Motor SG90',
                'descripcion' => 'Micro servo de 9g para proyectos de robótica',
                'categoria_id' => 5, // Desarrollo Móvil
                'codigo_producto' => 'SERVO-SG90',
                'marca' => 'TowerPro',
                'modelo' => 'SG90',
                'especificaciones' => json_encode([
                    'peso' => '9g',
                    'torque' => '1.8 kg/cm',
                    'velocidad' => '0.1 s/60°',
                    'voltaje' => '4.8V - 6V'
                ]),
                'precio' => 15.00,
                'stock' => 40,
                'stock_minimo' => 8,
                'proveedor' => 'TowerPro',
                'estado' => 'Disponible',
                
                
            ],
            [
                'nombre' => 'Kit de LEDs 5mm (100 piezas)',
                'descripcion' => 'Surtido de LEDs de colores de 5mm',
                'categoria_id' => 1,
                'codigo_producto' => 'LED-KIT-100',
                'marca' => 'Generic',
                'modelo' => 'LED-5MM',
                'especificaciones' => json_encode([
                    'colores' => ['Rojo', 'Verde', 'Azul', 'Amarillo', 'Blanco'],
                    'voltaje' => '2V - 3.3V',
                    'corriente' => '20mA',
                    'cantidad' => 100
                ]),
                'precio' => 20.00,
                'stock' => 50,
                'stock_minimo' => 10,
                'proveedor' => 'LED World',
                'estado' => 'Disponible',
                
                
            ],
            [
                'nombre' => 'Multímetro Digital DT830B',
                'descripcion' => 'Multímetro básico para mediciones eléctricas',
                'categoria_id' => 1,
                'codigo_producto' => 'MULTI-DT830B',
                'marca' => 'Generic',
                'modelo' => 'DT830B',
                'especificaciones' => json_encode([
                    'voltaje_dc' => '200mV - 1000V',
                    'voltaje_ac' => '200V - 750V',
                    'corriente_dc' => '200μA - 200mA',
                    'resistencia' => '200Ω - 2MΩ',
                    'pantalla' => 'LCD 3½ dígitos'
                ]),
                'precio' => 35.00,
                'stock' => 20,
                'stock_minimo' => 3,
                'proveedor' => 'Tool Master',
                'estado' => 'Disponible',
                
                
            ],
        ];

        foreach ($componentes as $componente) {
            DB::table('components')->insert($componente);
        }
    }
}
