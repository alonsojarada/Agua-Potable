<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Recibo extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($recibo) {
            // Generar folio único del recibo
            $recibo->folio_recibo = 'REC-' . date('Ym') . '-' . strtoupper(Str::random(5));
            
            // Generar token encriptado único para el QR basado en los datos y la app.key
            $dataToHash = $recibo->usuario_id . '-' . $recibo->monto . '-' . microtime();
            $recibo->token_seguridad = hash_hmac('sha256', $dataToHash, config('app.key'));
        });
    }

    // Relación con el usuario o toma de agua
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}