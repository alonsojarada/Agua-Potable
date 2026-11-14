<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tarifa extends Model
{
    use HasFactory;

    protected $table = 'tarifas';

    protected $fillable = [
        'nombre',
        'monto_mensual',
        'descripcion',
    ];

    // Relación opcional: una tarifa puede tener muchos usuarios (tomas) asociados
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'tarifa_id');
    }
}