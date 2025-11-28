<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario administrador por defecto
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@techhome.com',
            'password' => bcrypt('admin123'),
            'tipo_usuario' => 'admin',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario docente de prueba
        User::create([
            'name' => 'Docente Ejemplo',
            'email' => 'docente@techhome.com', 
            'password' => bcrypt('docente123'),
            'tipo_usuario' => 'docente',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario estudiante de prueba
        User::create([
            'name' => 'Estudiante Ejemplo',
            'email' => 'estudiante@techhome.com',
            'password' => bcrypt('estudiante123'),
            'tipo_usuario' => 'estudiante',
            'activo' => true,
            'email_verified_at' => now(),
        ]);
    }
}
