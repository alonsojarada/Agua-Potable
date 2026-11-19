<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center min-w-0">
            <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight truncate min-w-0" title="Gestión de Horarios de Suministro">
                {{ __('Gestión de Horarios de Suministro') }}
            </h2>
        </div>
    </x-slot>

    <style>
        .horarios-vista-fluida {
            font-size: clamp(0.65rem, 1.25vw, 0.875rem);
        }
        .horarios-vista-fluida .titulo-fluido {
            font-size: clamp(0.7rem, 1.35vw, 0.95rem);
        }
        .horarios-vista-fluida .label-fluido {
            font-size: clamp(0.6rem, 1.1vw, 0.78rem);
        }
        .horarios-vista-fluida .control-fluido {
            font-size: clamp(0.62rem, 1.15vw, 0.85rem);
            padding: clamp(0.22rem, 0.5vw, 0.45rem) clamp(0.35rem, 0.75vw, 0.65rem);
        }
        .horarios-vista-fluida table th,
        .horarios-vista-fluida table td {
            font-size: clamp(0.58rem, 1.15vw, 0.875rem);
            padding: clamp(0.25rem, 0.6vw, 0.65rem) clamp(0.3rem, 0.8vw, 1rem);
        }
        .horarios-vista-fluida .badge-horario-fluido {
            font-size: clamp(0.54rem, 1vw, 0.75rem);
            padding: clamp(0.12rem, 0.35vw, 0.22rem) clamp(0.32rem, 0.65vw, 0.6rem);
        }
        .horarios-vista-fluida .btn-icono-fluido {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(1.45rem, 2.3vw, 1.85rem);
            height: clamp(1.45rem, 2.3vw, 1.85rem);
            padding: 0;
        }
        .horarios-vista-fluida .btn-icono-fluido svg {
            width: clamp(0.78rem, 1.35vw, 0.95rem);
            height: clamp(0.78rem, 1.35vw, 0.95rem);
        }
    </style>

    <!-- CONTENEDOR ADAPTATIVO (Ajustado al contenido) -->
    <div class="py-1.5 sm:py-2 px-1.5 sm:px-3 w-full horarios-vista-fluida">
        <div class="bg-transparent overflow-hidden rounded-lg p-0 w-full flex flex-col">
            
            @if(session('success'))
                <div class="mb-2 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 px-3 py-1.5 rounded text-xs shrink-0" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- DISEÑO RESPONSIVO DE DOS COLUMNAS -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-2.5 sm:gap-3 items-start">
                
                <!-- ================= COLUMNA IZQUIERDA: FORMULARIO (2x2 SOLO EN MÓVIL/VENTANAS CHICAS) ================= -->
                <div class="lg:col-span-4 xl:col-span-3 bg-gray-50 border border-gray-200 rounded-lg p-2.5 sm:p-3.5 shadow-2xs flex flex-col w-full">
                    <h3 class="font-bold text-gray-800 titulo-fluido mb-2 sm:mb-3 uppercase">Programar Horario</h3>
                    
                    <form action="{{ route('horarios.store') }}" method="POST" id="horario-form" class="overflow-x-auto w-full">
                        @csrf
                        
                        <div class="space-y-2 sm:space-y-3 min-w-[230px] sm:min-w-0">
                            <!-- Fila 1: Circuito/Red y Día de la Semana (2 columnas en chico, 1 columna desde lg) -->
                            <div class="grid grid-cols-2 lg:grid-cols-1 gap-2 sm:gap-3">
                                <div class="min-w-0">
                                    <label class="block label-fluido font-medium text-gray-700 mb-0.5 sm:mb-1 truncate">Circuito / Red</label>
                                    <select name="red_id" required class="w-full rounded-md border-gray-300 shadow-2xs control-fluido focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Seleccione una red...</option>
                                        @foreach($redes as $red)
                                            <option value="{{ $red->id }}">{{ $red->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="min-w-0">
                                    <label class="block label-fluido font-medium text-gray-700 mb-0.5 sm:mb-1 truncate">Día de la Semana</label>
                                    <select name="dia_semana" required class="w-full rounded-md border-gray-300 shadow-2xs control-fluido focus:border-blue-500 focus:ring-blue-500">
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
                            </div>

                            <!-- Fila 2: Hora Apertura y Hora Cierre (2 columnas en chico, 1 columna desde lg) -->
                            <div class="grid grid-cols-2 lg:grid-cols-1 gap-2 sm:gap-3">
                                <div class="min-w-0">
                                    <label class="block label-fluido font-medium text-gray-700 mb-0.5 sm:mb-1 truncate">Hora de Apertura</label>
                                    <input type="time" name="hora_inicio" required class="w-full rounded-md border-gray-300 shadow-2xs control-fluido focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div class="min-w-0">
                                    <label class="block label-fluido font-medium text-gray-700 mb-0.5 sm:mb-1 truncate">Hora de Cierre</label>
                                    <input type="time" name="hora_fin" required class="w-full rounded-md border-gray-300 shadow-2xs control-fluido focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Botón Guardar de tamaño fijo -->
                            <div class="pt-1 sm:pt-2 flex justify-end lg:justify-start">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-1.5 text-xs rounded-md shadow-2xs transition cursor-pointer shrink-0 whitespace-nowrap">
                                    + Registrar Horario
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ================= COLUMNA DERECHA: TABLA E ITINERARIOS ================= -->
                <div class="lg:col-span-8 xl:col-span-9 bg-white border border-gray-200 rounded-lg p-2.5 sm:p-3.5 shadow-2xs flex flex-col w-full">
                    
                    <!-- Cabecera de la tabla con el botón Volver al Mapa a la derecha -->
                    <div class="flex justify-between items-center gap-2 mb-2.5 sm:mb-3 pb-2 border-b border-gray-200 shrink-0">
                        <h3 class="font-bold text-gray-800 titulo-fluido uppercase truncate">Itinerarios de Riego y Suministro Activos</h3>
                        <a href="{{ route('map.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white control-fluido rounded-md font-semibold shadow-2xs transition inline-flex items-center justify-center cursor-pointer shrink-0 whitespace-nowrap">
                            &larr; Volver al Mapa
                        </a>
                    </div>

                    <!-- Contenedor de la tabla con scroll horizontal si se reduce mucho -->
                    <div class="overflow-x-auto rounded-md border border-gray-200 w-full">
                        <table class="w-full min-w-[300px] divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left font-medium text-gray-500 uppercase tracking-wider">Circuito / Red</th>
                                    <th class="text-left font-medium text-gray-500 uppercase tracking-wider">Día(s)</th>
                                    <th class="text-left font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                                    <th class="text-center font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($horarios as $horario)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="font-medium text-gray-900 whitespace-nowrap">{{ $horario->red->nombre ?? 'N/A' }}</td>
                                        <td class="text-gray-700 font-semibold whitespace-nowrap">{{ $horario->dia_semana }}</td>
                                        <td class="text-gray-600 whitespace-nowrap">
                                            <span class="badge-horario-fluido bg-emerald-100 text-emerald-800 rounded-full font-bold inline-block whitespace-nowrap">
                                                {{ date('H:i', strtotime($horario->hora_inicio)) }} - {{ date('H:i', strtotime($horario->hora_fin)) }} hrs
                                            </span>
                                        </td>
                                        <td class="text-center whitespace-nowrap">
                                            <form action="{{ route('horarios.destroy', $horario->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este horario?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    title="Eliminar horario"
                                                    aria-label="Eliminar horario"
                                                    class="btn-icono-fluido text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 rounded-md transition cursor-pointer">
                                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-gray-400 italic">
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