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
                
            ],
            [
                'clave' => 'email_contacto',
                'valor' => 'contacto@techhome.com',
                'descripcion' => 'Email principal de contacto',
                'tipo' => 'email',
                
            ],
            [
                'clave' => 'telefono_contacto',
                'valor' => '+591 7 123-4567',
                'descripcion' => 'Teléfono de contacto principal',
                'tipo' => 'telefono',
                
            ],
            [
                'clave' => 'direccion',
                'valor' => 'Av. Principal 123, Santa Cruz, Bolivia',
                'descripcion' => 'Dirección física de la institución',
                'tipo' => 'texto',
                
            ],
            [
                'clave' => 'limite_intentos_login',
                'valor' => '5',
                'descripcion' => 'Número máximo de intentos de login fallidos',
                'tipo' => 'numero',
                
            ],
            [
                'clave' => 'tiempo_bloqueo_minutos',
                'valor' => '30',
                'descripcion' => 'Tiempo en minutos que dura el bloqueo por intentos fallidos',
                'tipo' => 'numero',
                
            ],
            [
                'clave' => 'dias_acceso_invitado',
                'valor' => '3',
                'descripcion' => 'Días de acceso gratuito para usuarios invitados',
                'tipo' => 'numero',
                
            ],
            [
                'clave' => 'activar_2fa',
                'valor' => 'true',
                'descripcion' => 'Activar autenticación de dos factores',
                'tipo' => 'booleano',
                
            ],
            [
                'clave' => 'tiempo_expiracion_otp',
                'valor' => '300',
                'descripcion' => 'Tiempo de expiración de códigos OTP en segundos',
                'tipo' => 'numero',
                
            ],
            [
                'clave' => 'smtp_servidor',
                'valor' => 'smtp.gmail.com',
                'descripcion' => 'Servidor SMTP para envío de emails',
                'tipo' => 'texto',
                
            ],
            [
                'clave' => 'smtp_puerto',
                'valor' => '587',
                'descripcion' => 'Puerto del servidor SMTP',
                'tipo' => 'numero',
                
            ],
            [
                'clave' => 'smtp_usuario',
                'valor' => 'noreply@techhome.com',
                'descripcion' => 'Usuario para autenticación SMTP',
                'tipo' => 'email',
                
            ],
            [
                'clave' => 'moneda_sistema',
                'valor' => 'BOB',
                'descripcion' => 'Moneda principal del sistema',
                'tipo' => 'texto',
                
            ],
            [
                'clave' => 'iva_porcentaje',
                'valor' => '13',
                'descripcion' => 'Porcentaje de IVA aplicado a las ventas',
                'tipo' => 'numero',
                
            ],
            [
                'clave' => 'stock_minimo_alerta',
                'valor' => '10',
                'descripcion' => 'Cantidad mínima de stock para generar alertas',
                'tipo' => 'numero',
                
            ],
            [
                'clave' => 'modo_mantenimiento',
                'valor' => 'false',
                'descripcion' => 'Activar modo de mantenimiento del sitio',
                'tipo' => 'booleano',
                
            ],
            [
                'clave' => 'version_sistema',
                'valor' => '2.0.0',
                'descripcion' => 'Versión actual del sistema',
                'tipo' => 'texto',
                
            ],
            [
                'clave' => 'zona_horaria',
                'valor' => 'America/La_Paz',
                'descripcion' => 'Zona horaria del sistema',
                'tipo' => 'texto',
                
            ],
            [
                'clave' => 'idioma_defecto',
                'valor' => 'es',
                'descripcion' => 'Idioma por defecto del sistema',
                'tipo' => 'texto',
                
            ],
            [
                'clave' => 'activar_registro_publico',
                'valor' => 'true',
                'descripcion' => 'Permitir registro público de nuevos usuarios',
                'tipo' => 'booleano',
                
            ],
        ];

        foreach ($configuraciones as $config) {
            DB::table('configurations')->insert($config);
        }
    }
}
