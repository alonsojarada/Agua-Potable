<?php

namespace App\Http\Controllers;

use App\Models\Red;
use App\Models\Usuario;
use App\Models\RedTramo;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $redes = Red::with(['tramos', 'usuarios', 'horarios'])->get();
        return view('map.index', compact('redes'));
    }

    public function general()
    {
        $redes = Red::with(['tramos', 'usuarios', 'horarios'])->get();
        $usuarios = Usuario::with('red')->get();

        return view('map.index', compact('redes', 'usuarios')); // Cambia 'map.general' por el nombre de tu archivo existente
    }
}