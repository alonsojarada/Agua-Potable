<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center min-w-0">
            <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight truncate min-w-0" title="Padrón de Tomas de Agua (Usuarios)">
                {{ __('Padrón de Tomas de Agua (Usuarios)') }}
            </h2>
        </div>
    </x-slot>

    <!-- CSS de Leaflet para el mapa del modal -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- CONTENEDOR ADAPTATIVO (Ajustado al contenido) -->
    <div class="py-1.5 sm:py-2 px-1.5 sm:px-3 w-full">
        <div class="bg-transparent overflow-hidden rounded-lg p-0 w-full flex flex-col">

            @if(session('success'))
                <div class="mb-2 bg-green-100 border border-green-400 text-green-700 px-3 py-1.5 rounded text-xs shrink-0">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tarjeta Principal con la Tabla -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-2xs flex flex-col overflow-hidden p-2 sm:p-3">
                
                <!-- Botón "+ Nueva Toma" alineado a la derecha arriba de la tabla -->
                <div class="flex justify-end items-center mb-2 sm:mb-3 shrink-0">
                    <a href="{{ route('usuarios.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-3.5 py-1.5 rounded-md text-xs sm:text-sm font-semibold shadow-2xs transition inline-flex items-center justify-center cursor-pointer">
                        + Nueva Toma
                    </a>
                </div>

                <!-- Contenedor de la tabla -->
                <div class="overflow-x-auto rounded-md border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-[11px] sm:text-xs md:text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="hidden sm:table-cell px-2.5 md:px-4 py-2 md:py-2.5 text-left font-medium text-gray-500 uppercase tracking-wider">Folio</th>
                                <th class="px-2 sm:px-3 md:px-4 py-2 md:py-2.5 text-left font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="hidden md:table-cell px-4 py-2.5 text-left font-medium text-gray-500 uppercase tracking-wider">Red Asignada</th>
                                <th class="px-2 sm:px-3 md:px-4 py-2 md:py-2.5 text-left font-medium text-gray-500 uppercase tracking-wider">Tarifa</th>
                                <th class="px-2 sm:px-3 md:px-4 py-2 md:py-2.5 text-center font-medium text-gray-500 uppercase tracking-wider">Estatus</th>
                                <th class="px-1.5 sm:px-3 md:px-4 py-2 md:py-2.5 text-right font-medium text-gray-500 uppercase tracking-wider w-24 sm:w-28">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($usuarios as $usuario)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="hidden sm:table-cell px-2.5 md:px-4 py-2 md:py-3 whitespace-nowrap font-bold text-gray-900">
                                        {{ $usuario->folio }}
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-4 py-2 md:py-3">
                                        <div class="font-medium text-gray-900 leading-tight">{{ $usuario->nombre_completo }}</div>
                                        @if($usuario->direccion)
                                            <div class="hidden sm:block text-[11px] text-gray-500 truncate max-w-xs">{{ $usuario->direccion }}</div>
                                        @endif
                                    </td>
                                    <td class="hidden md:table-cell px-4 py-3 whitespace-nowrap text-gray-600">
                                        {{ $usuario->red->nombre ?? 'Sin red' }}
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-4 py-2 md:py-3 whitespace-nowrap text-gray-700 font-semibold">
                                        @if($usuario->tarifa)
                                            ${{ number_format($usuario->tarifa->monto_mensual, 2) }}
                                        @else
                                            <span class="text-[10px] sm:text-xs text-gray-400 italic font-normal">Sin tarifa</span>
                                        @endif
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-4 py-2 md:py-3 whitespace-nowrap text-center">
                                        @if($usuario->estado_servicio === 'activo')
                                            <span class="px-1.5 sm:px-2 inline-flex text-[10px] sm:text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                        @elseif($usuario->estado_servicio === 'suspendido')
                                            <span class="px-1.5 sm:px-2 inline-flex text-[10px] sm:text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Suspendido</span>
                                        @else
                                            <span class="px-1.5 sm:px-2 inline-flex text-[10px] sm:text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cortado</span>
                                        @endif
                                    </td>
                                    <td class="px-1.5 sm:px-3 md:px-4 py-2 md:py-3 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center justify-end gap-1">
                                            <!-- Botón Ver Ubicación -->
                                            <button type="button"
                                                title="Ver ubicación"
                                                aria-label="Ver ubicación"
                                                onclick="abrirModalToma(
                                                    '{{ $usuario->folio }}', 
                                                    '{{ addslashes($usuario->nombre_completo) }}', 
                                                    '{{ $usuario->latitud }}', 
                                                    '{{ $usuario->longitud }}'
                                                )"
                                                class="w-6 h-6 sm:w-7 sm:h-7 inline-flex items-center justify-center rounded-md bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-800 transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- Botón Editar -->
                                            <a href="{{ route('usuarios.edit', $usuario) }}"
                                                title="Editar toma"
                                                aria-label="Editar toma"
                                                class="w-6 h-6 sm:w-7 sm:h-7 inline-flex items-center justify-center rounded-md bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-800 transition">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <!-- Botón Eliminar -->
                                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('¿Estás seguro de eliminar esta toma?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    title="Eliminar toma"
                                                    aria-label="Eliminar toma"
                                                    class="w-6 h-6 sm:w-7 sm:h-7 inline-flex items-center justify-center rounded-md bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 transition cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 sm:px-6 py-8 text-center text-xs sm:text-sm text-gray-500">
                                        No hay tomas registradas en el padrón todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-3 shrink-0">
                    {{ $usuarios->links() }}
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL VISTA RÁPIDA (SOLO MAPA Y MARCADOR) -->
    <div id="modal-toma" onclick="if(event.target === this) cerrarModalToma()"
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-60 flex items-center justify-center p-3 sm:p-4"
        style="display: none;">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-full sm:max-w-xl w-full p-3 sm:p-4">
            <div class="flex justify-between items-center pb-2 mb-2 border-b">
                <h3 class="text-xs sm:text-sm font-bold text-gray-800 truncate pr-2">
                    <span id="modal-folio" class="text-blue-600"></span> -
                    <span id="modal-nombre"></span>
                </h3>
                <button type="button" onclick="cerrarModalToma()"
                    class="text-gray-400 hover:text-gray-700 font-bold text-xl leading-none cursor-pointer">&times;</button>
            </div>

            <!-- Contenedor Único del Mapa -->
            <div id="mapa-detalle" class="w-full h-[260px] sm:h-[360px] rounded-md border shadow-inner"></div>
        </div>
    </div>

    <!-- JS de Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        let mapaModal = null;
        let marcadorModal = null;

        function abrirModalToma(folio, nombre, lat, lng) {
            document.getElementById('modal-folio').innerText = folio;
            document.getElementById('modal-nombre').innerText = nombre;
            document.getElementById('modal-toma').style.display = 'flex';

            let latitude = lat ? parseFloat(lat) : 25.3263827;
            let longitude = lng ? parseFloat(lng) : -103.295072;

            setTimeout(() => {
                if (!mapaModal) {
                    mapaModal = L.map('mapa-detalle').setView([latitude, longitude], 17);
                    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        maxZoom: 19,
                        attribution: 'Esri'
                    }).addTo(mapaModal);
                } else {
                    mapaModal.setView([latitude, longitude], 17);
                }

                mapaModal.invalidateSize();

                if (marcadorModal) {
                    mapaModal.removeLayer(marcadorModal);
                }

                if (lat && lng) {
                    marcadorModal = L.marker([latitude, longitude]).addTo(mapaModal);
                }
            }, 100);
        }

        function cerrarModalToma() {
            document.getElementById('modal-toma').style.display = 'none';
        }
    </script>
</x-app-layout>