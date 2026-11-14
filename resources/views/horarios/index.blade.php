<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center min-w-0">
            <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight truncate min-w-0" title="Gestión de Horarios de Suministro">
                {{ __('Gestión de Horarios de Suministro') }}
            </h2>
        </div>
    </x-slot>

    <!-- CONTENEDOR ADAPTATIVO (Ajustado al contenido) -->
    <div class="py-2 px-2 sm:px-3 w-full">
        <div class="bg-transparent overflow-hidden rounded-lg p-0 w-full flex flex-col">
            
            @if(session('success'))
                <div class="mb-2 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 px-3 py-1.5 rounded text-xs shrink-0" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- DISEÑO RESPONSIVO DE DOS COLUMNAS -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-start">
                
                <!-- ================= COLUMNA IZQUIERDA: FORMULARIO ================= -->
                <div class="lg:col-span-4 xl:col-span-3 bg-gray-50 border border-gray-200 rounded-lg p-3.5 shadow-2xs flex flex-col">
                    <h3 class="font-bold text-gray-800 text-xs sm:text-sm mb-3 uppercase">Programar Horario</h3>
                    
                    <form action="{{ route('horarios.store') }}" method="POST" id="horario-form" class="space-y-3">
                        @csrf
                        
                        <!-- Seleccionar Red -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Circuito / Red</label>
                            <select name="red_id" required class="w-full rounded-md border-gray-300 shadow-2xs text-xs sm:text-sm py-2 px-2.5 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione una red...</option>
                                @foreach($redes as $red)
                                    <option value="{{ $red->id }}">{{ $red->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Día de la semana -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Día de la Semana</label>
                            <select name="dia_semana" required class="w-full rounded-md border-gray-300 shadow-2xs text-xs sm:text-sm py-2 px-2.5 focus:border-blue-500 focus:ring-blue-500">
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
                            <label class="block text-xs font-medium text-gray-700 mb-1">Hora de Apertura</label>
                            <input type="time" name="hora_inicio" required class="w-full rounded-md border-gray-300 shadow-2xs text-xs sm:text-sm py-2 px-2.5 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Hora Fin -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Hora de Cierre</label>
                            <input type="time" name="hora_fin" required class="w-full rounded-md border-gray-300 shadow-2xs text-xs sm:text-sm py-2 px-2.5 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Botón Guardar -->
                        <div class="pt-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-3 rounded-md text-xs sm:text-sm shadow-2xs transition cursor-pointer">
                                + Registrar Horario
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= COLUMNA DERECHA: TABLA E ITINERARIOS ================= -->
                <div class="lg:col-span-8 xl:col-span-9 bg-white border border-gray-200 rounded-lg p-3.5 shadow-2xs flex flex-col">
                    
                    <!-- Cabecera de la tabla con el botón Volver al Mapa a la derecha -->
                    <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-200 shrink-0">
                        <h3 class="font-bold text-gray-800 text-xs sm:text-sm uppercase">Itinerarios de Riego y Suministro Activos</h3>
                        <a href="{{ route('map.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3.5 py-1.5 rounded-md text-xs sm:text-sm font-semibold shadow-2xs transition inline-flex items-center justify-center cursor-pointer shrink-0">
                            &larr; Volver al Mapa
                        </a>
                    </div>

                    <!-- Contenedor de la tabla (fluido sin estirarse de más) -->
                    <div class="overflow-x-auto rounded-md border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-medium text-gray-500 uppercase tracking-wider">Circuito / Red</th>
                                    <th class="px-4 py-2.5 text-left font-medium text-gray-500 uppercase tracking-wider">Día(s)</th>
                                    <th class="px-4 py-2.5 text-left font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                                    <th class="px-4 py-2.5 text-center font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($horarios as $horario)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $horario->red->nombre ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-gray-700 font-semibold">{{ $horario->dia_semana }}</td>
                                        <td class="px-4 py-3 text-gray-600">
                                            <span class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full text-xs font-bold">
                                                {{ date('H:i', strtotime($horario->hora_inicio)) }} hrs - {{ date('H:i', strtotime($horario->hora_fin)) }} hrs
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <form action="{{ route('horarios.destroy', $horario->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este horario?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-xs bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded transition cursor-pointer">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic text-sm">
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
    </div>
</x-app-layout>