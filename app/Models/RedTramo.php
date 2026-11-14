<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedTramo extends Model
{
    use HasFactory;

    protected $table = 'redes_tramos';
    protected $fillable = ['red_id', 'nombre_tramo', 'coordenadas_linea', 'diametro_pulgadas'];

    protected $casts = [
        'coordenadas_linea' => 'array', // Convierte automáticamente el JSON a Array de PHP
    ];

    public function red()
    {
        return $this->belongsTo(Red::class);
    }
}
