<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Red;
use App\Models\Tarifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with(['red', 'tarifa'])->paginate(10);

        // Cargar las redes y tarifas que la vista index necesita en sus modales/formularios
        $redes = Red::all();
        $tarifas = Tarifa::all();

        return view('usuarios.index', compact('usuarios', 'redes', 'tarifas'));
    }

    /**
     * Muestra el formulario para registrar una nueva toma.
     */
    public function create()
    {
        $redes = Red::all();
        $tarifas = Tarifa::all();

        return view('usuarios.create', compact('redes', 'tarifas'));
    }

    /**
     * Almacena una nueva toma en la base de datos.
     */

    public function store(Request $request)
    {
        // 1. Validar los datos de entrada (incluyendo la tarifa)
        $request->validate([
            'red_id' => 'required|exists:redes,id',
            'tarifa_id' => 'required|exists:tarifas,id', // <-- Validación agregada
            'nombre_completo' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',    // <-- Opcional
            'direccion' => 'nullable|string|max:255', // <-- Opcional
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'estado_servicio' => 'required|in:activo,suspendido,cortado',
        ]);

        // 2. Generar el folio de forma automática y atómica justo en el momento de guardar
        $folioGenerado = DB::transaction(function () {
            $ultimoId = Usuario::max('id') ?? 0;
            $siguienteNumero = $ultimoId + 1;

            return 'TOMA-' . str_pad($siguienteNumero, 3, '0', STR_PAD_LEFT);
        });

        // 3. Fusionar el folio generado con los datos del request para guardarlos juntos
        $datos = $request->all();
        $datos['folio'] = $folioGenerado;

        Usuario::create($datos);

        return redirect()->route('usuarios.index')->with('success', 'Toma de agua registrada correctamente con el folio ' . $folioGenerado);
    }

    /**
     * Muestra el detalle de una toma.
     */
    public function show(Usuario $usuario)
    {
        $usuario->load(['red', 'pagos']);
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Usuario $usuario)
    {
        $redes = Red::all();    // O como consultes tus redes
        $tarifas = Tarifa::all(); // <--- ¡Asegúrate de incluir esto aquí también!

        return view('usuarios.edit', compact('usuario', 'redes', 'tarifas'));
    }
    /**
     * Actualiza la toma de agua.
     */
    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'red_id' => 'required|exists:redes,id',
            'tarifa_id' => 'required|exists:tarifas,id', // <-- Validación agregada
            'folio' => 'required|string|max:50|unique:usuarios,folio,' . $usuario->id,
            'nombre_completo' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'estado_servicio' => 'required|in:activo,suspendido,cortado',
        ]);

        $usuario->update($request->all());

        return redirect()->route('usuarios.index')->with('success', 'Toma de agua actualizada correctamente.');
    }

    /**
     * Elimina una toma del padrón.
     */
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Toma de agua eliminada correctamente.');
    }
}
