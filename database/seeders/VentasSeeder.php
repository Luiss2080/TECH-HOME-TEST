<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primero creamos las ventas
        $ventas = [
            [
                'numero_venta' => 'VTA-2025-001',
                'cliente_id' => 3, // Estudiante
                'vendedor_id' => 2, // Docente
                'subtotal' => 180.00,
                'descuento' => 0.00,
                'impuestos' => 23.40,
                'total' => 203.40,
                'tipo_pago' => 'Efectivo',
                'estado' => 'Completada',
                'notas' => 'Compra de materiales educativos',
                'fecha_venta' => now()->subDays(15),
                'fecha_actualizacion' => now()->subDays(15),
            ],
            [
                'numero_venta' => 'VTA-2025-002',
                'cliente_id' => 3,
                'vendedor_id' => 2,
                'subtotal' => 85.00,
                'descuento' => 8.50,
                'impuestos' => 9.95,
                'total' => 86.45,
                'tipo_pago' => 'Transferencia',
                'estado' => 'Completada',
                'notas' => 'Descuento por estudiante',
                'fecha_venta' => now()->subDays(12),
                'fecha_actualizacion' => now()->subDays(12),
            ],
            [
                'numero_venta' => 'VTA-2025-003',
                'cliente_id' => 3,
                'vendedor_id' => 1, // Admin
                'subtotal' => 350.00,
                'descuento' => 35.00,
                'impuestos' => 40.95,
                'total' => 355.95,
                'tipo_pago' => 'Tarjeta',
                'estado' => 'Completada',
                'notas' => 'Kit completo de Arduino',
                'fecha_venta' => now()->subDays(8),
                'fecha_actualizacion' => now()->subDays(8),
            ],
            [
                'numero_venta' => 'VTA-2025-004',
                'cliente_id' => 3,
                'vendedor_id' => 2,
                'subtotal' => 270.00,
                'descuento' => 0.00,
                'impuestos' => 35.10,
                'total' => 305.10,
                'tipo_pago' => 'QR',
                'estado' => 'Completada',
                'notas' => 'Proyecto de robótica',
                'fecha_venta' => now()->subDays(5),
                'fecha_actualizacion' => now()->subDays(5),
            ],
            [
                'numero_venta' => 'VTA-2025-005',
                'cliente_id' => 3,
                'vendedor_id' => 2,
                'subtotal' => 99.90,
                'descuento' => 0.00,
                'impuestos' => 12.99,
                'total' => 112.89,
                'tipo_pago' => 'Efectivo',
                'estado' => 'Pendiente',
                'notas' => 'Pendiente de entrega',
                'fecha_venta' => now()->subDays(2),
                'fecha_actualizacion' => now()->subDays(2),
            ],
        ];

        foreach ($ventas as $venta) {
            DB::table('ventas')->insert($venta);
        }

        // Ahora creamos los detalles de venta
        $detalles_ventas = [
            // Detalles para VTA-2025-001
            [
                'venta_id' => 1,
                'producto_tipo' => 'libro',
                'producto_id' => 1, // HTML5 y CSS3 para Principiantes
                'cantidad' => 2,
                'precio_unitario' => 45.00,
                'subtotal' => 90.00,
                'descripcion_producto' => 'HTML5 y CSS3 para Principiantes',
            ],
            [
                'venta_id' => 1,
                'producto_tipo' => 'libro',
                'producto_id' => 2, // Bases de Datos Relacionales
                'cantidad' => 1,
                'precio_unitario' => 65.00,
                'subtotal' => 65.00,
                'descripcion_producto' => 'Bases de Datos Relacionales',
            ],
            [
                'venta_id' => 1,
                'producto_tipo' => 'componente',
                'producto_id' => 5, // Sensor DHT22
                'cantidad' => 1,
                'precio_unitario' => 12.00,
                'subtotal' => 12.00,
                'descripcion_producto' => 'Sensor de Temperatura DHT22',
            ],

            // Detalles para VTA-2025-002
            [
                'venta_id' => 2,
                'producto_tipo' => 'componente',
                'producto_id' => 1, // Arduino UNO
                'cantidad' => 1,
                'precio_unitario' => 45.00,
                'subtotal' => 45.00,
                'descripcion_producto' => 'Arduino UNO R3',
            ],
            [
                'venta_id' => 2,
                'producto_tipo' => 'componente',
                'producto_id' => 4, // Sensor Ultrasónico
                'cantidad' => 5,
                'precio_unitario' => 8.00,
                'subtotal' => 40.00,
                'descripcion_producto' => 'Sensor Ultrasónico HC-SR04',
            ],

            // Detalles para VTA-2025-003
            [
                'venta_id' => 3,
                'producto_tipo' => 'componente',
                'producto_id' => 2, // Raspberry Pi 4
                'cantidad' => 1,
                'precio_unitario' => 120.00,
                'subtotal' => 120.00,
                'descripcion_producto' => 'Raspberry Pi 4 Model B',
            ],
            [
                'venta_id' => 3,
                'producto_tipo' => 'libro',
                'producto_id' => 4, // Machine Learning con Python
                'cantidad' => 1,
                'precio_unitario' => 85.00,
                'subtotal' => 85.00,
                'descripcion_producto' => 'Machine Learning con Python',
            ],
            [
                'venta_id' => 3,
                'producto_tipo' => 'componente',
                'producto_id' => 3, // ESP32
                'cantidad' => 6,
                'precio_unitario' => 25.00,
                'subtotal' => 150.00,
                'descripcion_producto' => 'ESP32 DevKit V1',
            ],

            // Detalles para VTA-2025-004
            [
                'venta_id' => 4,
                'producto_tipo' => 'libro',
                'producto_id' => 5, // Desarrollo Android
                'cantidad' => 2,
                'precio_unitario' => 70.00,
                'subtotal' => 140.00,
                'descripcion_producto' => 'Desarrollo de Apps Android',
            ],
            [
                'venta_id' => 4,
                'producto_tipo' => 'componente',
                'producto_id' => 6, // Servo Motor
                'cantidad' => 8,
                'precio_unitario' => 15.00,
                'subtotal' => 120.00,
                'descripcion_producto' => 'Servo Motor SG90',
            ],

            // Detalles para VTA-2025-005
            [
                'venta_id' => 5,
                'producto_tipo' => 'libro',
                'producto_id' => 3, // Ciberseguridad
                'cantidad' => 1,
                'precio_unitario' => 75.00,
                'subtotal' => 75.00,
                'descripcion_producto' => 'Ciberseguridad Práctica',
            ],
            [
                'venta_id' => 5,
                'producto_tipo' => 'componente',
                'producto_id' => 7, // LEDs
                'cantidad' => 1,
                'precio_unitario' => 20.00,
                'subtotal' => 20.00,
                'descripcion_producto' => 'Kit de LEDs 5mm (100 piezas)',
            ],
        ];

        foreach ($detalles_ventas as $detalle) {
            DB::table('detalle_ventas')->insert($detalle);
        }
    }
}
