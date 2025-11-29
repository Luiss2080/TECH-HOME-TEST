<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpCode extends Model
{
    use HasFactory;

    protected $table = 'otp_codes';
    
    protected $fillable = [
        'usuario_id',
        'codigo',
        'expira_en',
        'utilizado'
    ];

    protected $casts = [
        'expira_en' => 'datetime',
        'utilizado' => 'boolean',
        'creado_en' => 'datetime'
    ];

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null; // No tiene updated_at

    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Generar un código OTP
     */
    public static function generarCodigo($usuarioId, $minutosVida = 10): self
    {
        // Invalidar códigos anteriores no utilizados
        static::where('usuario_id', $usuarioId)
              ->where('utilizado', false)
              ->update(['utilizado' => true]);

        // Crear nuevo código
        return static::create([
            'usuario_id' => $usuarioId,
            'codigo' => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expira_en' => now()->addMinutes($minutosVida),
            'utilizado' => false
        ]);
    }

    /**
     * Verificar un código OTP
     */
    public static function verificarCodigo($usuarioId, $codigo): ?self
    {
        $otpCode = static::where('usuario_id', $usuarioId)
                        ->where('codigo', $codigo)
                        ->where('utilizado', false)
                        ->where('expira_en', '>', now())
                        ->first();

        if ($otpCode) {
            $otpCode->update(['utilizado' => true]);
            return $otpCode;
        }

        return null;
    }

    /**
     * Scope para códigos válidos
     */
    public function scopeValidos($query)
    {
        return $query->where('utilizado', false)
                    ->where('expira_en', '>', now());
    }

    /**
     * Scope para códigos expirados
     */
    public function scopeExpirados($query)
    {
        return $query->where('expira_en', '<', now());
    }

    /**
     * Verificar si el código está expirado
     */
    public function estaExpirado(): bool
    {
        return $this->expira_en < now();
    }

    /**
     * Verificar si el código está utilizado
     */
    public function estaUtilizado(): bool
    {
        return $this->utilizado;
    }

    /**
     * Marcar como utilizado
     */
    public function marcarComoUtilizado(): bool
    {
        return $this->update(['utilizado' => true]);
    }
}