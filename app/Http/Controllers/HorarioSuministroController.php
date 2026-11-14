<?php

namespace App\Http\Controllers;

use App\Models\HorarioSuministro;
use App\Models\Red;
use Illuminate\Http\Request;

class HorarioSuministroController extends Controller
{
    public function index()
    {
        $horarios = HorarioSuministro::with('red')->get();
        $redes = Red::all();
        return view('horarios.index', compact('horarios', 'redes'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'red_id' => 'required|exists:redes,id',
            'dia_semana' => 'required|string',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
            'observaciones' => 'nullable|string|max:255',
        ]);

        HorarioSuministro::create($request->all());

        return redirect()->route('horarios.index')->with('success', 'Horario de suministro registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(HorarioSuministro $horario)
    {
        $horario->delete();
        return redirect()->route('horarios.index')->with('success', 'Horario eliminado correctamente.');
    }
}
