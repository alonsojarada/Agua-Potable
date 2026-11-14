<?php

namespace App\Http\Controllers;

use App\Models\Red;
use Illuminate\Http\Request;

class RedController extends Controller
{
    /**
     * Muestra el listado de redes.
     */
    public function index()
    {
        $redes = Red::withCount(['horarios', 'tramos', 'usuarios'])->get();
        return view('redes.index', compact('redes'));
    }

    /**
     * Muestra el formulario para crear una nueva red.
     */
    public function create()
    {
        return view('redes.create');
    }

    /**
     * Almacena una nueva red en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'nombre_tramo' => 'required|string|max:255',
            'coordenadas_linea' => 'required|string', // JSON string del mapa
            'diametro_pulgadas' => 'required|numeric',
        ]);

        // 1. Crear la Red
        $red = Red::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        // 2. Crear automáticamente el tramo físico asociado con sus coordenadas
        \App\Models\RedTramo::create([
            'red_id' => $red->id,
            'nombre_tramo' => $request->nombre_tramo,
            'coordenadas_linea' => json_decode($request->coordenadas_linea, true),
            'diametro_pulgadas' => $request->diametro_pulgadas,
        ]);

        return redirect()->route('redes.index')->with('success', 'Red de agua y trazo de tubería guardados correctamente.');
    }

    /**
     * Muestra los detalles de una red específica.
     */
    public function show(Red $rede)
    {
        $rede->load(['horarios', 'tramos', 'usuarios']);
        return view('redes.show', compact('rede'));
    }

    /**
     * Muestra el formulario para editar una red.
     */
    public function edit(Red $rede)
    {
        // Cargamos la red con sus tramos para obtener las coordenadas existentes
        $rede->load('tramos');
        $tramoInicial = $rede->tramos->first(); // Tomamos el tramo principal o de referencia

        return view('redes.edit', compact('rede', 'tramoInicial'));
    }

    /**
     * Actualiza una red existente en la base de datos.
     */
    public function update(Request $request, Red $rede)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'nombre_tramo' => 'required|string|max:255',
            'coordenadas_linea' => 'required|string', // JSON string actualizado desde el mapa
            'diametro_pulgadas' => 'required|numeric',
        ]);

        // 1. Actualizar datos de la Red
        $rede->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        // 2. Actualizar o crear el tramo principal asociado
        $rede->tramos()->updateOrCreate(
            ['red_id' => $rede->id], // Condición de búsqueda
            [
                'nombre_tramo' => $request->nombre_tramo,
                'coordenadas_linea' => json_decode($request->coordenadas_linea, true),
                'diametro_pulgadas' => $request->diametro_pulgadas,
            ]
        );

        return redirect()->route('redes.index')->with('success', 'Red de agua y trazo actualizados correctamente.');
    }

    /**
     * Elimina una red de la base de datos.
     */
    public function destroy(Red $rede)
    {
        $rede->delete();

        return redirect()->route('redes.index')->with('success', 'Red de agua eliminada correctamente.');
    }
}
