<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivationToken extends Model
{
    use HasFactory;

    protected $table = 'activation_tokens';
    
    protected $fillable = [
        'email',
        'token',
        'usado',
        'fecha_creacion'
    ];

    public $timestamps = false;

    protected $casts = [
        'usado' => 'boolean',
        'fecha_creacion' => 'datetime'
    ];

    /**
     * Crear un token de activación
     */
    public static function createToken(string $email): string
    {
        // Generar token único
        $token = bin2hex(random_bytes(32));
        
        // Eliminar tokens anteriores del mismo email que no hayan sido usados
        static::where('email', $email)
              ->where('usado', false)
              ->delete();
        
        // Crear nuevo token
        static::create([
            'email' => $email,
            'token' => $token,
            'usado' => false,
            'fecha_creacion' => now()
        ]);
        
        return $token;
    }

    /**
     * Validar un token de activación
     */
    public static function validateToken(string $token): ?static
    {
        return static::where('token', $token)
                    ->where('usado', false)
                    ->first();
    }

    /**
     * Marcar token como usado
     */
    public static function markAsUsed(string $token): bool
    {
        $activationToken = static::where('token', $token)->first();
        
        if (!$activationToken) {
            return false;
        }
        
        return $activationToken->update(['usado' => true]);
    }

    /**
     * Obtener token por email
     */
    public static function getByEmail(string $email): ?static
    {
        return static::where('email', $email)
                    ->where('usado', false)
                    ->orderBy('fecha_creacion', 'desc')
                    ->first();
    }

    /**
     * Eliminar tokens expirados (opcional - para limpieza)
     */
    public static function deleteExpiredTokens(): int
    {
        // Eliminar tokens usados de más de 30 días
        return static::where('usado', true)
                    ->where('fecha_creacion', '<', now()->subDays(30))
                    ->delete();
    }
}
