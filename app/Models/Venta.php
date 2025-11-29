<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'sales';
    
    protected $fillable = [
        'numero_venta',
        'cliente_id',
        'vendedor_id',
        'subtotal',
        'impuestos',
        'descuento',
        'total',
        'estado',
        'metodo_pago',
        'fecha_venta',
        'notas'
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id', 'id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id', 'id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id', 'id');
    }

    // Scopes
    public static function completadas()
    {
        return self::where('estado', '=', 'Completada');
    }

    public static function delMes($mes = null, $año = null)
    {
        $mes = $mes ?: date('m');
        $año = $año ?: date('Y');
        
        return self::whereRaw('MONTH(fecha_venta) = ? AND YEAR(fecha_venta) = ?', [$mes, $año]);
    }

    public static function recientes(int $dias = 7)
    {
        return self::whereRaw('fecha_venta >= DATE_SUB(NOW(), INTERVAL ? DAY)', [$dias])
                   ->orderBy('fecha_venta', 'desc');
    }
}
