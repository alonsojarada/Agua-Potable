<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Horarios de Suministro') }}
            </h2>
            <a href="{{ route('map.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-semibold shadow">
                &larr; Volver al Mapa
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Mensaje de éxito -->
            @if(session('success'))
                <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Formulario de Registro de Horario -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-gray-800 text-lg mb-4">Programar Nuevo Horario de Suministro</h3>
                
                <form action="{{ route('horarios.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @csrf
                    
                    <!-- Seleccionar Red -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Circuito / Red</label>
                        <select name="red_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Seleccione una red...</option>
                            @foreach($redes as $red)
                                <option value="{{ $red->id }}">{{ $red->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Día de la semana -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Día de la Semana</label>
                        <select name="dia_semana" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sábado">Sábado</option>
                            <option value="Domingo">Domingo</option>
                            <option value="Lunes a Viernes">Lunes a Viernes</option>
                            <option value="Fines de semana">Fines de semana</option>
                        </select>
                    </div>

                    <!-- Hora Inicio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora de Apertura</label>
                        <input type="time" name="hora_inicio" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>

                    <!-- Hora Fin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora de Cierre</label>
                        <input type="time" name="hora_fin" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>

                    <!-- Botón Guardar -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow text-sm transition">
                            + Registrar Horario
                        </button>
                    </div>
                </form>
            </div>

            <!-- Listado de Horarios Registrados -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-gray-800 text-lg mb-4">Itinerarios de Riego y Suministro Activos</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Circuito / Red</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Día(s)</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Horario</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-600 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($horarios as $horario)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $horario->red->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-gray-700 font-semibold">{{ $horario->dia_semana }}</td>
                                    <td class="px-6 py-4 text-gray-600">
                                        <span class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full text-xs font-bold">
                                            {{ date('H:i', strtotime($horario->hora_inicio)) }} hrs - {{ date('H:i', strtotime($horario->hora_fin)) }} hrs
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('horarios.destroy', $horario->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este horario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded transition">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">
                                        No hay horarios de suministro registrados por el momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>