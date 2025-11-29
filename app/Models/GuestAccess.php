<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestAccess extends Model
{
    use HasFactory;

    protected $table = 'guest_access';
    
    protected $fillable = [
        'usuario_id',
        'fecha_inicio',
        'fecha_vencimiento',
        'dias_restantes',
        'ultima_notificacion',
        'notificaciones_enviadas',
        'acceso_bloqueado'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date',
        'ultima_notificacion' => 'date',
        'dias_restantes' => 'integer',
        'notificaciones_enviadas' => 'json',
        'acceso_bloqueado' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Scope para accesos vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->where('fecha_vencimiento', '<', now()->toDateString());
    }

    /**
     * Scope para accesos próximos a vencer
     */
    public function scopeProximosAVencer($query, $dias = 3)
    {
        return $query->whereBetween('fecha_vencimiento', [
            now()->toDateString(),
            now()->addDays($dias)->toDateString()
        ]);
    }

    /**
     * Scope para accesos no bloqueados
     */
    public function scopeNoBloqueados($query)
    {
        return $query->where('acceso_bloqueado', false);
    }

    /**
     * Verificar si el acceso está vencido
     */
    public function estaVencido(): bool
    {
        return $this->fecha_vencimiento < now()->toDateString();
    }

    /**
     * Verificar si está próximo a vencer
     */
    public function proximoAVencer($dias = 3): bool
    {
        $diasRestantes = now()->diffInDays($this->fecha_vencimiento, false);
        return $diasRestantes >= 0 && $diasRestantes <= $dias;
    }

    /**
     * Calcular días restantes
     */
    public function calcularDiasRestantes(): int
    {
        return max(0, now()->diffInDays($this->fecha_vencimiento, false));
    }

    /**
     * Bloquear acceso
     */
    public function bloquearAcceso(): bool
    {
        return $this->update(['acceso_bloqueado' => true]);
    }

    /**
     * Desbloquear acceso
     */
    public function desbloquearAcceso(): bool
    {
        return $this->update(['acceso_bloqueado' => false]);
    }

    /**
     * Extender acceso
     */
    public function extenderAcceso($dias): bool
    {
        $nuevaFecha = $this->fecha_vencimiento->addDays($dias);
        
        return $this->update([
            'fecha_vencimiento' => $nuevaFecha,
            'dias_restantes' => $this->calcularDiasRestantes(),
            'acceso_bloqueado' => false
        ]);
    }
}