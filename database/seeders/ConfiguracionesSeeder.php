<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuraciones = [
            [
                'clave' => 'nombre_sitio',
                'valor' => 'TECH-HOME',
                'descripcion' => 'Nombre del sitio web',
                'tipo' => 'texto',
                'updated_at' => now(),
            ],
            [
                'clave' => 'email_contacto',
                'valor' => 'contacto@techhome.com',
                'descripcion' => 'Email principal de contacto',
                'tipo' => 'email',
                'updated_at' => now(),
            ],
            [
                'clave' => 'telefono_contacto',
                'valor' => '+591 7 123-4567',
                'descripcion' => 'Teléfono de contacto principal',
                'tipo' => 'telefono',
                'updated_at' => now(),
            ],
            [
                'clave' => 'direccion',
                'valor' => 'Av. Principal 123, Santa Cruz, Bolivia',
                'descripcion' => 'Dirección física de la institución',
                'tipo' => 'texto',
                'updated_at' => now(),
            ],
            [
                'clave' => 'limite_intentos_login',
                'valor' => '5',
                'descripcion' => 'Número máximo de intentos de login fallidos',
                'tipo' => 'numero',
                'updated_at' => now(),
            ],
            [
                'clave' => 'tiempo_bloqueo_minutos',
                'valor' => '30',
                'descripcion' => 'Tiempo en minutos que dura el bloqueo por intentos fallidos',
                'tipo' => 'numero',
                'updated_at' => now(),
            ],
            [
                'clave' => 'dias_acceso_invitado',
                'valor' => '3',
                'descripcion' => 'Días de acceso gratuito para usuarios invitados',
                'tipo' => 'numero',
                'updated_at' => now(),
            ],
            [
                'clave' => 'activar_2fa',
                'valor' => 'true',
                'descripcion' => 'Activar autenticación de dos factores',
                'tipo' => 'booleano',
                'updated_at' => now(),
            ],
            [
                'clave' => 'tiempo_expiracion_otp',
                'valor' => '300',
                'descripcion' => 'Tiempo de expiración de códigos OTP en segundos',
                'tipo' => 'numero',
                'updated_at' => now(),
            ],
            [
                'clave' => 'smtp_servidor',
                'valor' => 'smtp.gmail.com',
                'descripcion' => 'Servidor SMTP para envío de emails',
                'tipo' => 'texto',
                'updated_at' => now(),
            ],
            [
                'clave' => 'smtp_puerto',
                'valor' => '587',
                'descripcion' => 'Puerto del servidor SMTP',
                'tipo' => 'numero',
                'updated_at' => now(),
            ],
            [
                'clave' => 'smtp_usuario',
                'valor' => 'noreply@techhome.com',
                'descripcion' => 'Usuario para autenticación SMTP',
                'tipo' => 'email',
                'updated_at' => now(),
            ],
            [
                'clave' => 'moneda_sistema',
                'valor' => 'BOB',
                'descripcion' => 'Moneda principal del sistema',
                'tipo' => 'texto',
                'updated_at' => now(),
            ],
            [
                'clave' => 'iva_porcentaje',
                'valor' => '13',
                'descripcion' => 'Porcentaje de IVA aplicado a las ventas',
                'tipo' => 'numero',
                'updated_at' => now(),
            ],
            [
                'clave' => 'stock_minimo_alerta',
                'valor' => '10',
                'descripcion' => 'Cantidad mínima de stock para generar alertas',
                'tipo' => 'numero',
                'updated_at' => now(),
            ],
            [
                'clave' => 'modo_mantenimiento',
                'valor' => 'false',
                'descripcion' => 'Activar modo de mantenimiento del sitio',
                'tipo' => 'booleano',
                'updated_at' => now(),
            ],
            [
                'clave' => 'version_sistema',
                'valor' => '2.0.0',
                'descripcion' => 'Versión actual del sistema',
                'tipo' => 'texto',
                'updated_at' => now(),
            ],
            [
                'clave' => 'zona_horaria',
                'valor' => 'America/La_Paz',
                'descripcion' => 'Zona horaria del sistema',
                'tipo' => 'texto',
                'updated_at' => now(),
            ],
            [
                'clave' => 'idioma_defecto',
                'valor' => 'es',
                'descripcion' => 'Idioma por defecto del sistema',
                'tipo' => 'texto',
                'updated_at' => now(),
            ],
            [
                'clave' => 'activar_registro_publico',
                'valor' => 'true',
                'descripcion' => 'Permitir registro público de nuevos usuarios',
                'tipo' => 'booleano',
                'updated_at' => now(),
            ],
        ];

        foreach ($configuraciones as $config) {
            DB::table('configurations')->insert($config);
        }
    }
}