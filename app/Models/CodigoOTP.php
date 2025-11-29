<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CodigoOTP extends Model
{
    protected $table = 'codigos_otp';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'usuario_id',
        'codigo',
        'expira_en',
        'utilizado',
        'creado_en'
    ];
    
    protected $casts = [
        'expira_en' => 'datetime',
        'creado_en' => 'datetime',
        'utilizado' => 'integer',
        'usuario_id' => 'integer'
    ];
    
    public $timestamps = false;

    // Configuración OTP
    const EXPIRATION_MINUTES = 1; // 1 minuto = 60 segundos
    const MAX_ATTEMPTS = 3;
    const LOCKOUT_MINUTES = 5;
    const CODE_LENGTH = 6;

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Generar un nuevo código OTP para un usuario
     */
    public static function generateOTP($usuarioId): array
    {
        try {
            DB::beginTransaction();

            // Invalidar códigos anteriores del usuario
            self::invalidatePreviousCodes($usuarioId);

            // Generar código de 6 dígitos
            $codigo = self::generateSecureCode();

            // Calcular fecha de expiración (60 segundos)
            $expiraEn = now()->addMinutes(self::EXPIRATION_MINUTES);

            // Crear nuevo código
            $otp = static::create([
                'usuario_id' => $usuarioId,
                'codigo' => $codigo,
                'expira_en' => $expiraEn,
                'utilizado' => 0,
                'creado_en' => now()
            ]);

            if ($otp) {
                DB::commit();
                return [
                    'success' => true,
                    'codigo' => $codigo,
                    'expira_en' => $expiraEn->toDateTimeString(),
                    'expira_en_timestamp' => $expiraEn->timestamp
                ];
            }

            DB::rollBack();
            return ['success' => false, 'error' => 'Error al guardar código OTP'];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generando OTP: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error interno generando código'];
        }
    }

    /**
     * Validar un código OTP
     */
    public static function validateOTP($usuarioId, $codigo): array
    {
        try {
            // Verificar si el usuario está bloqueado
            $user = User::find($usuarioId);
            if (!$user) {
                return ['success' => false, 'error' => 'Usuario no encontrado'];
            }

            if ($user->isBlocked()) {
                $tiempoRestante = $user->getBlockTimeRemaining();
                return [
                    'success' => false, 
                    'error' => "Usuario bloqueado. Intenta en {$tiempoRestante} minutos.",
                    'locked' => true,
                    'unlock_time' => $user->bloqueado_hasta->toDateTimeString()
                ];
            }

            // Buscar código OTP válido
            $otpRecord = static::where('usuario_id', $usuarioId)
                              ->where('codigo', $codigo)
                              ->where('utilizado', 0)
                              ->orderBy('creado_en', 'DESC')
                              ->first();

            if (!$otpRecord) {
                // Incrementar intentos fallidos
                self::incrementFailedAttempts($usuarioId);
                return ['success' => false, 'error' => 'Código incorrecto o no existe'];
            }

            // Verificar expiración
            if (now() > $otpRecord->expira_en) {
                self::incrementFailedAttempts($usuarioId);
                return ['success' => false, 'error' => 'El código ha expirado'];
            }

            // ¡Código válido! Marcar como utilizado
            $otpRecord->update(['utilizado' => 1]);

            // Resetear intentos fallidos del usuario
            $user->update([
                'intentos_fallidos' => 0,
                'bloqueado_hasta' => null
            ]);

            return ['success' => true, 'message' => 'Código validado correctamente'];

        } catch (\Exception $e) {
            Log::error('Error validando OTP: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error interno validando código'];
        }
    }

    /**
     * Verificar si se puede generar un nuevo código
     */
    public static function canGenerateNewCode($usuarioId): array
    {
        try {
            // Verificar si hay un código activo no utilizado
            $activeCode = static::where('usuario_id', $usuarioId)
                               ->where('utilizado', 0)
                               ->where('expira_en', '>', now())
                               ->first();

            if ($activeCode) {
                $tiempoRestante = now()->diffInSeconds($activeCode->expira_en);
                return [
                    'can_generate' => false,
                    'reason' => "Ya hay un código activo. Expira en {$tiempoRestante} segundos.",
                    'codigo_existente' => true,
                    'expira_en' => $activeCode->expira_en->timestamp
                ];
            }

            // Verificar si el usuario está bloqueado
            $user = User::find($usuarioId);
            if ($user && $user->isBlocked()) {
                $tiempoRestante = $user->getBlockTimeRemaining();
                return [
                    'can_generate' => false,
                    'reason' => "Usuario bloqueado por intentos fallidos. Intenta en {$tiempoRestante} minutos.",
                    'bloqueado' => true
                ];
            }

            return ['can_generate' => true];

        } catch (\Exception $e) {
            Log::error('Error verificando si se puede generar código: ' . $e->getMessage());
            return ['can_generate' => false, 'reason' => 'Error interno'];
        }
    }

    /**
     * Reenviar código OTP
     */
    public static function resendOTP($usuarioId): array
    {
        $canGenerate = self::canGenerateNewCode($usuarioId);
        
        if (!$canGenerate['can_generate']) {
            return ['success' => false, 'reason' => $canGenerate['reason']];
        }

        return self::generateOTP($usuarioId);
    }

    /**
     * Generar código seguro de 6 dígitos
     */
    private static function generateSecureCode(): string
    {
        return str_pad(random_int(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Invalidar códigos anteriores del usuario
     */
    private static function invalidatePreviousCodes($usuarioId): void
    {
        static::where('usuario_id', $usuarioId)
              ->where('utilizado', 0)
              ->update(['utilizado' => 1]);
    }

    /**
     * Incrementar intentos fallidos
     */
    private static function incrementFailedAttempts($usuarioId): void
    {
        try {
            $user = User::find($usuarioId);
            if ($user) {
                $user->increment('intentos_fallidos');

                // Bloquear si excede MAX_ATTEMPTS
                if ($user->intentos_fallidos >= self::MAX_ATTEMPTS) {
                    $user->update([
                        'bloqueado_hasta' => now()->addMinutes(self::LOCKOUT_MINUTES)
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error incrementando intentos fallidos: ' . $e->getMessage());
        }
    }

    /**
     * Limpiar códigos expirados
     */
    public static function cleanExpired(): int
    {
        return static::where('expira_en', '<', now()->subHours(1))->delete();
    }

    /**
     * Scope para códigos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('utilizado', 0)->where('expira_en', '>', now());
    }

    /**
     * Scope para códigos de un usuario
     */
    public function scopeForUser($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }
}