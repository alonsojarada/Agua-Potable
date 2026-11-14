<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $fillable = [
        'usuario_id', 'periodo_mes', 'periodo_anio', 
        'monto', 'fecha_pago', 'estatus'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
