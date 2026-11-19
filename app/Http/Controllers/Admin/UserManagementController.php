<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->orderBy('name');

        if ($request->filled('buscar')) {
            $termino = trim((string) $request->input('buscar'));
            $query->where(function ($q) use ($termino) {
                $q->where('name', 'like', "%{$termino}%")
                    ->orWhere('email', 'like', "%{$termino}%");
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), ['activo', 'inactivo'], true)) {
            $query->where('status', $request->input('status'));
        }

        $usuarios = $query->paginate(12)->withQueryString();

        return view('admin.cuentas.index', [
            'usuarios' => $usuarios,
            'filtros' => $request->only(['buscar', 'status']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'status' => ['nullable', 'string', Rule::in(['activo', 'inactivo'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol' => 'operador',
            'status' => $validated['status'] ?? 'activo',
        ]);

        return redirect()
            ->route('admin.cuentas.index')
            ->with('status', 'Usuario dado de alta correctamente.');
    }

    public function updateStatus(Request $request, User $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['activo', 'inactivo'])],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if ((int) $request->user()->id === (int) $usuario->id && $validated['status'] !== 'activo') {
            return back()->withErrors([
                'status' => 'No puedes desactivar tu propia cuenta mientras estás en sesión.',
            ]);
        }

        $datos = [
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $datos['password'] = Hash::make($validated['password']);
        }

        $usuario->update($datos);

        return redirect()
            ->route('admin.cuentas.index')
            ->with('status', "Estatus de {$usuario->name} actualizado a {$validated['status']}.");
    }
}