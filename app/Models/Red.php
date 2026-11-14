<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Red extends Model
{
    //
    use HasFactory;

    protected $table = 'redes';
    protected $fillable = ['nombre', 'descripcion'];

    public function horarios()
    {
        return $this->hasMany(HorarioSuministro::class);
    }

    public function tramos()
    {
        return $this->hasMany(RedTramo::class);
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }
}
