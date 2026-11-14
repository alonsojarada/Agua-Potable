<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Pago;
use Carbon\Carbon;

class GenerarPagosMensuales extends Command
{
    protected $signature = 'pagos:generar-mensuales';
    protected $description = 'Genera automáticamente el pago pendiente del mes en curso para todos los usuarios activos, respetando pagos existentes o adelantados.';

    public function handle()
    {
        $anioActual = Carbon::now()->year;
        $mesActualNum = Carbon::now()->month;
        
        $mesesAnio = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // Nombre del mes actual exacto (ej. Noviembre)
        $nombreMesActual = $mesesAnio[$mesActualNum];

        // Obtenemos solo usuarios activos (excluyendo bajas)
        $usuarios = Usuario::where('estado_servicio', '!=', 'baja')->with('tarifa')->get();

        $creados = 0;

        foreach ($usuarios as $usuario) {
            // Verificamos únicamente si ya existe un registro para el mes en curso y año actual
            $existe = Pago::where('usuario_id', $usuario->id)
                ->where('periodo_mes', $nombreMesActual)
                ->where('periodo_anio', $anioActual)
                ->exists();

            // Si no existe, lo creamos como pendiente
            if (!$existe) {
                Pago::create([
                    'usuario_id' => $usuario->id,
                    'periodo_mes' => $nombreMesActual,
                    'periodo_anio' => $anioActual,
                    'monto' => $usuario->tarifa->monto_mensual ?? 0,
                    'estatus' => 'pendiente',
                ]);
                $creados++;
            }
        }

        $this->info("¡Proceso exitoso! Se generó el pago pendiente de {$nombreMesActual} {$anioActual} para {$creados} usuarios activos.");
    }
}