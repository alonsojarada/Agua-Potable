<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioSuministro extends Model
{
    //
    use HasFactory;

    protected $table = 'horarios_suministro';
    protected $fillable = ['red_id', 'dia_semana', 'hora_inicio', 'hora_fin'];

    public function red()
    {
        return $this->belongsTo(Red::class);
    }
}
