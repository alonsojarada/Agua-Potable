<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $fillable = [
        'red_id',
        'tarifa_id',
        'folio',
        'nombre_completo',
        'telefono',
        'direccion',
        'latitud',
        'longitud',
        'estado_servicio',
    ];

    public function red()
    {
        return $this->belongsTo(Red::class);
    }

    // Relación con la tarifa mensual asignada
    public function tarifa()
    {
        return $this->belongsTo(Tarifa::class, 'tarifa_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function verificarYGenerarPagosPendientes()
    {
        // Si el usuario está dado de baja, no generamos nada
        if (strtolower($this->estado_servicio) === 'baja') {
            return;
        }

        $anioActual = date('Y');
        $mesActualNum = (int) date('n');

        $mesesAnio = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        $nombreMesActual = $mesesAnio[$mesActualNum];

        // Verificamos si ya existe el registro físico del mes en curso (sea pendiente o pagado)
        $pagoExistente = $this->pagos()
            ->where('periodo_mes', $nombreMesActual)
            ->where('periodo_anio', $anioActual)
            ->first();

        // Si NO existe físicamente en la base de datos, lo creamos como pendiente de inmediato
        if (!$pagoExistente) {
            \App\Models\Pago::create([
                'usuario_id' => $this->id,
                'periodo_mes' => $nombreMesActual,
                'periodo_anio' => $anioActual,
                'monto' => $this->tarifa->monto_mensual ?? 0,
                'estatus' => 'pendiente',
            ]);
        }
    }
}