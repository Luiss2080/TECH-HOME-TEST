<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookDownload extends Model
{
    protected $table = 'book_downloads';
    
    protected $fillable = [
        'usuario_id',
        'libro_id',
        'ip_address',
        'user_agent',
        'fecha_descarga'
    ];
    
    protected $casts = [
        'fecha_descarga' => 'datetime'
    ];

    const CREATED_AT = 'fecha_descarga';
    const UPDATED_AT = null;

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
}