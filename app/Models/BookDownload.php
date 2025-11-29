<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookDownload extends Model
{
    protected $table = 'book_downloads';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'usuario_id',
        'libro_id',
        'fecha_descarga',
        'ip_address',
        'user_agent'
    ];
    
    protected $casts = [
        'usuario_id' => 'integer',
        'libro_id' => 'integer',
        'fecha_descarga' => 'datetime'
    ];
    
    public $timestamps = false;

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Usuario que descargó el libro
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Libro descargado
     */
    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope por usuario
     */
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Scope por libro
     */
    public function scopePorLibro($query, $libroId)
    {
        return $query->where('libro_id', $libroId);
    }

    /**
     * Scope por fecha
     */
    public function scopePorFecha($query, $fechaDesde, $fechaHasta = null)
    {
        $query->whereDate('fecha_descarga', '>=', $fechaDesde);
        
        if ($fechaHasta) {
            $query->whereDate('fecha_descarga', '<=', $fechaHasta);
        }
        
        return $query;
    }

    /**
     * Scope para descargas recientes
     */
    public function scopeRecientes($query, $days = 30)
    {
        return $query->where('fecha_descarga', '>=', now()->subDays($days));
    }
}