<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActivationToken extends Model
{
    protected $table = 'activation_tokens';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'email',
        'token',
        'usado',
        'fecha_creacion'
    ];
    
    protected $casts = [
        'usado' => 'integer',
        'fecha_creacion' => 'datetime'
    ];
    
    public $timestamps = false;

    /**
     * Crear un token de activación
     */
    public static function createToken(string $email): string
    {
        // Generar token único
        $token = bin2hex(random_bytes(32));
        
        // Eliminar tokens anteriores del mismo email que no hayan sido usados
        static::where('email', $email)->where('usado', 0)->delete();
        
        // Crear nuevo token
        static::create([
            'email' => $email,
            'token' => $token,
            'usado' => 0,
            'fecha_creacion' => now()
        ]);
        
        return $token;
    }

    /**
     * Validar un token de activación
     */
    public static function validateToken(string $token): ?array
    {
        $activationToken = static::where('token', $token)->first();
        
        if (!$activationToken) {
            return null;
        }

        // Verificar si ya fue usado
        if ($activationToken->usado == 1) {
            return ['error' => 'El token ya ha sido utilizado.'];
        }

        // Verificar expiración (24 horas)
        $creationTime = $activationToken->fecha_creacion;
        $expirationTime = $creationTime->addHours(24);
        
        if (now() > $expirationTime) {
            return ['error' => 'El token ha expirado.'];
        }

        return ['success' => true, 'email' => $activationToken->email];
    }

    /**
     * Marcar token como usado
     */
    public static function markAsUsed(string $token): bool
    {
        $updated = static::where('token', $token)->update(['usado' => 1]);
        return $updated > 0;
    }

    /**
     * Limpiar tokens expirados
     */
    public static function cleanExpired(): int
    {
        $expiredTime = now()->subHours(24);
        return static::where('fecha_creacion', '<', $expiredTime)->delete();
    }

    /**
     * Obtener token activo por email
     */
    public static function getActiveTokenByEmail(string $email): ?self
    {
        return static::where('email', $email)
                     ->where('usado', 0)
                     ->where('fecha_creacion', '>', now()->subHours(24))
                     ->first();
    }
}