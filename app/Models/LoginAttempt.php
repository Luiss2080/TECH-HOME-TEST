<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
    use HasFactory;

    protected $table = 'login_attempts';
    
    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'exitoso',
        'intentado_en'
    ];

    protected $casts = [
        'exitoso' => 'boolean',
        'intentado_en' => 'datetime'
    ];

    public $timestamps = false;

    /**
     * Registrar intento de login
     */
    public static function registrar($email, $exitoso = false)
    {
        return static::create([
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'exitoso' => $exitoso,
            'intentado_en' => now()
        ]);
    }

    /**
     * Obtener intentos fallidos recientes para un email
     */
    public static function intentosFallidos($email, $minutos = 15)
    {
        return static::where('email', $email)
                    ->where('exitoso', false)
                    ->where('intentado_en', '>', now()->subMinutes($minutos))
                    ->count();
    }

    /**
     * Obtener intentos fallidos por IP
     */
    public static function intentosFallidosPorIP($ip, $minutos = 15)
    {
        return static::where('ip_address', $ip)
                    ->where('exitoso', false)
                    ->where('intentado_en', '>', now()->subMinutes($minutos))
                    ->count();
    }

    /**
     * Limpiar intentos antiguos
     */
    public static function limpiarAntiguos($dias = 30)
    {
        return static::where('intentado_en', '<', now()->subDays($dias))->delete();
    }
}