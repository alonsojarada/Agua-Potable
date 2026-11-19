<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2 min-w-0">
            <h2 class="font-semibold text-sm sm:text-lg text-slate-800 leading-tight truncate">
                {{ __('Cuentas de Acceso del Sistema') }}
            </h2>
            <span class="px-2 py-0.5 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-[11px] font-semibold shrink-0">
                Solo Administrador
            </span>
        </div>
    </x-slot>

    <div class="py-3 sm:py-5 px-2.5 sm:px-5 max-w-7xl mx-auto space-y-3.5 sm:space-y-4">
        @if (session('status'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-2 rounded-lg text-xs font-medium">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-3 py-2 rounded-lg text-xs">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li class="break-words">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 sm:gap-5 items-start">
            <!-- ALTA DE NUEVA CUENTA (COMPACTO) -->
            <div class="lg:col-span-4 bg-white border border-slate-200 rounded-xl p-3 sm:p-4 shadow-2xs min-w-0">
                <h3 class="text-xs sm:text-sm font-bold text-slate-900">
                    Dar de Alta Usuario
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5 mb-2.5 leading-snug">
                    Registra un nuevo usuario y define su estatus inicial.
                </p>

                <form method="POST" action="{{ route('admin.cuentas.store') }}" class="space-y-2">
                    @csrf

                    <div>
                        <label for="name" class="block text-[11px] font-semibold text-slate-700 mb-0.5">Nombre completo</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                            class="w-full rounded-lg border-slate-300 text-xs py-1.5 px-2.5 focus:border-teal-600 focus:ring-teal-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2">
                        <div>
                            <label for="email" class="block text-[11px] font-semibold text-slate-700 mb-0.5">Correo electrónico</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                class="w-full rounded-lg border-slate-300 text-xs py-1.5 px-2.5 focus:border-teal-600 focus:ring-teal-600">
                        </div>

                        <div>
                            <label for="status" class="block text-[11px] font-semibold text-slate-700 mb-0.5">Estatus inicial</label>
                            <select id="status" name="status" required
                                class="w-full rounded-lg border-slate-300 text-xs py-1.5 px-2.5 focus:border-teal-600 focus:ring-teal-600">
                                <option value="activo" @selected(old('status', 'activo') === 'activo')>Activo</option>
                                <option value="inactivo" @selected(old('status') === 'inactivo')>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label for="password" class="block text-[11px] font-semibold text-slate-700 mb-0.5">Contraseña inicial</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                class="w-full rounded-lg border-slate-300 text-xs py-1.5 px-2.5 focus:border-teal-600 focus:ring-teal-600">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-[11px] font-semibold text-slate-700 mb-0.5">Confirmar clave</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                class="w-full rounded-lg border-slate-300 text-xs py-1.5 px-2.5 focus:border-teal-600 focus:ring-teal-600">
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="submit"
                            class="w-full py-1.5 sm:py-2 px-3 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-2xs transition cursor-pointer">
                            Registrar Usuario
                        </button>
                    </div>
                </form>
            </div>

            <!-- LISTADO Y CAMBIO DE ESTATUS (SIEMPRE EN TABLA COMPACTA) -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-xl p-3 sm:p-4 shadow-2xs space-y-3 min-w-0">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900">
                            Cuentas Registradas ({{ $usuarios->total() }})
                        </h3>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            Activa o desactiva el acceso de cada usuario.
                        </p>
                    </div>

                    <form method="GET" action="{{ route('admin.cuentas.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-1.5">
                        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}"
                            placeholder="Buscar nombre o correo..."
                            class="w-full sm:w-44 rounded-lg border-slate-300 text-xs py-1.5 px-2.5 focus:border-teal-600 focus:ring-teal-600">

                        <div class="flex items-center gap-1.5">
                            <select name="status"
                                class="flex-1 sm:flex-none rounded-lg border-slate-300 text-xs py-1.5 px-2 focus:border-teal-600 focus:ring-teal-600">
                                <option value="">Todos</option>
                                <option value="activo" @selected(($filtros['status'] ?? '') === 'activo')>Activos</option>
                                <option value="inactivo" @selected(($filtros['status'] ?? '') === 'inactivo')>Inactivos</option>
                            </select>

                            <button type="submit"
                                class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition shrink-0 cursor-pointer">
                                Filtrar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-lg">
                    <table class="min-w-[38rem] w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold uppercase text-slate-600">
                                <th class="py-2 px-2.5">Usuario</th>
                                <th class="py-2 px-2.5">Rol</th>
                                <th class="py-2 px-2.5">Estatus</th>
                                <th class="py-2 px-2.5">Nueva clave (opcional)</th>
                                <th class="py-2 px-2.5 text-right">Guardar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse ($usuarios as $u)
                                @php $formId = 'cuenta-form-' . $u->id; @endphp
                                <tr class="hover:bg-slate-50/60">
                                    <td class="py-2 px-2.5 align-middle">
                                        <div class="font-bold text-slate-900 leading-tight">{{ $u->name }}</div>
                                        <div class="text-[11px] text-slate-500 leading-tight break-all">{{ $u->email }}</div>
                                    </td>

                                    <td class="py-2 px-2.5 align-middle whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $u->isAdmin() ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ ucfirst($u->rol) }}
                                        </span>
                                    </td>

                                    <td class="py-2 px-2.5 align-middle whitespace-nowrap">
                                        <select name="status" form="{{ $formId }}"
                                            class="rounded-md border-slate-300 text-xs py-1 px-2 font-semibold focus:border-teal-600 focus:ring-teal-600 {{ $u->isActivo() ? 'text-emerald-700 bg-emerald-50/50' : 'text-red-700 bg-red-50/50' }}">
                                            <option value="activo" @selected($u->status === 'activo')>Activo</option>
                                            <option value="inactivo" @selected($u->status === 'inactivo')>Inactivo</option>
                                        </select>
                                    </td>

                                    <td class="py-2 px-2.5 align-middle whitespace-nowrap">
                                        <input type="password" name="password" form="{{ $formId }}"
                                            placeholder="Sin cambios" autocomplete="new-password"
                                            class="w-28 sm:w-32 rounded-md border-slate-300 text-xs py-1 px-2 focus:border-teal-600 focus:ring-teal-600">
                                    </td>

                                    <td class="py-2 px-2.5 align-middle text-right whitespace-nowrap">
                                        <form id="{{ $formId }}" method="POST" action="{{ route('admin.cuentas.status', $u) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="px-2.5 py-1 rounded-md bg-teal-600 hover:bg-teal-700 text-white text-[11px] font-semibold transition cursor-pointer">
                                                Actualizar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 px-3 text-center text-xs text-slate-500">
                                        No se encontraron cuentas con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="overflow-x-auto text-xs">
                    {{ $usuarios->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>