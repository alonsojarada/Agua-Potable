<?php

namespace App\Http\Controllers;

use App\Models\Tarifa;
use Illuminate\Http\Request;

class TarifaController extends Controller
{
    // 1. Retorna todas las tarifas en JSON para poblar la tabla del modal
    public function indexAjax()
    {
        return response()->json(Tarifa::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'monto_mensual' => 'required|numeric|min:0',
        ]);

        $tarifa = Tarifa::create([
            'nombre' => $request->nombre,
            'monto_mensual' => $request->monto_mensual,
            'descripcion' => $request->descripcion ?? null,
            'estatus' => true, // Nace activa como booleano
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tarifa creada con éxito',
            'tarifa' => $tarifa
        ]);
    }

    public function toggleStatus(Tarifa $tarifa)
    {
        // Invierte el valor booleano actual (true a false, o false a true)
        $tarifa->estatus = !$tarifa->estatus;
        $tarifa->save();

        return response()->json([
            'success' => true,
            'estatus' => $tarifa->estatus,
            'message' => 'Estatus actualizado correctamente'
        ]);
    }
}