<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $table = 'configurations';
    
    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
        'tipo',
        'editable'
    ];

    protected $casts = [
        'editable' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime'
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * Obtener una configuración por su clave
     */
    public static function getValueByClave($clave, $default = null)
    {
        $config = static::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    /**
     * Establecer una configuración
     */
    public static function setValueByClave($clave, $valor, $descripcion = null)
    {
        return static::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valor,
                'descripcion' => $descripcion,
                'tipo' => is_bool($valor) ? 'boolean' : (is_numeric($valor) ? 'numeric' : 'string')
            ]
        );
    }

    /**
     * Scope para configuraciones editables
     */
    public function scopeEditables($query)
    {
        return $query->where('editable', true);
    }

    /**
     * Obtener valor tipificado
     */
    public function getValorTipificadoAttribute()
    {
        switch ($this->tipo) {
            case 'boolean':
                return filter_var($this->valor, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $this->valor;
            case 'float':
            case 'numeric':
                return (float) $this->valor;
            case 'json':
                return json_decode($this->valor, true);
            default:
                return $this->valor;
        }
    }
}