<?php

namespace App\Http\Controllers;

use App\Models\Recibo;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReciboController extends Controller
{
    // 1. Mostrar el detalle o la vista imprimible del recibo con su QR
    public function show($id)
    {
        $recibo = Recibo::with('usuario')->findOrFail($id);
        return view('recibos.show', compact('recibo'));
    }

    // 2. Verificar el token del QR al escanearlo visualmente
    public function verificarPorQr($token)
    {
        $recibo = Recibo::with('usuario')->where('token_seguridad', $token)->firstOrFail();
        return view('recibos.verificar', compact('recibo'));
    }

    // 3. Mostrar la vista del escáner de caja con cámara web
    public function vistaEscanear()
    {
        return view('recibos.escanear');
    }

    // 4. API interna para verificar el token en formato JSON cuando la cámara lee el QR
    public function verificarPorQrJson($token)
    {
        $recibo = Recibo::with('usuario')->where('token_seguridad', $token)->first();

        if (!$recibo) {
            return response()->json([
                'success' => false,
                'message' => 'Código QR inválido o no reconocido en el sistema.'
            ]);
        }

        // Si ya está pagado, enviamos la información detallada del pago previo
        if ($recibo->estatus === 'pagado') {
            return response()->json([
                'success' => false,
                'ya_pagado' => true, // Bandera especial para identificarlo en el frontend
                'message' => '¡Atención! Este recibo ya fue liquidado anteriormente.',
                'recibo' => $recibo
            ]);
        }

        return response()->json([
            'success' => true,
            'recibo' => $recibo
        ]);
    }

    // 5. Procesar y confirmar el cobro una vez escaneado con la cámara de caja
    public function procesarCobroQr($id)
    {
        $recibo = Recibo::findOrFail($id);

        if ($recibo->estatus === 'pagado') {
            return back()->withErrors(['error' => 'El recibo ya se encuentra pagado.']);
        }

        DB::transaction(function () use ($recibo) {
            // Actualizar el recibo a pagado
            $recibo->update([
                'estatus' => 'pagado',
                'fecha_pago' => Carbon::now()
            ]);

            // Sincronizar y actualizar también el pago correspondiente en la tabla de pagos
            $pago = Pago::where('usuario_id', $recibo->usuario_id)
                ->where('periodo_mes', explode(' ', $recibo->mes_anio)[0])
                ->where('periodo_anio', explode(' ', $recibo->mes_anio)[1] ?? date('Y'))
                ->first();

            if ($pago) {
                $pago->update([
                    'estatus' => 'pagado',
                    'fecha_pago' => Carbon::now()
                ]);
            }
        });

        return redirect()->route('recibos.escanear')->with('success', '¡Cobro registrado y aplicado exitosamente mediante QR!');
    }
}