<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center min-w-0">
            <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight truncate min-w-0" title="Gestión de Redes de Agua">
                {{ __('Gestión de Redes de Agua') }}
            </h2>
        </div>
    </x-slot>

    <!-- CSS de Leaflet para el mapa del modal -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        .redes-index-fluida {
            font-size: clamp(0.62rem, 1.2vw, 0.875rem);
        }
        .redes-index-fluida table th,
        .redes-index-fluida table td {
            font-size: clamp(0.58rem, 1.15vw, 0.84rem);
            padding: clamp(0.25rem, 0.65vw, 0.65rem) clamp(0.25rem, 0.75vw, 0.85rem);
        }
        .redes-index-fluida .badge-fluido {
            font-size: clamp(0.56rem, 1vw, 0.75rem);
            padding: clamp(0.05rem, 0.25vw, 0.15rem) clamp(0.3rem, 0.6vw, 0.5rem);
        }
        .redes-index-fluida .btn-accion-icono {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(1.35rem, 2.2vw, 1.75rem);
            height: clamp(1.35rem, 2.2vw, 1.75rem);
            padding: 0;
        }
        .redes-index-fluida .btn-accion-icono svg {
            width: clamp(0.72rem, 1.25vw, 0.92rem);
            height: clamp(0.72rem, 1.25vw, 0.92rem);
        }

        /* En móviles (< 640px) mostrar solo Nombre, Horarios, Tomas y Acciones */
        @media (max-width: 639px) {
            .redes-index-fluida .solo-escritorio {
                display: none !important;
            }
        }
    </style>

    <!-- CONTENEDOR DE ANCHO COMPLETO Y DINÁMICO -->
    <div class="py-1.5 sm:py-2 px-1.5 sm:px-3 w-full redes-index-fluida">
        <div class="bg-white overflow-hidden shadow-2xs rounded-lg p-2 sm:p-3 w-full">
            
            @if(session('success'))
                <div class="mb-2 sm:mb-3 bg-green-100 border border-green-400 text-green-700 px-2.5 sm:px-3 py-1.5 rounded text-xs" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- BOTÓN NUEVA RED ALINEADO A LA DERECHA (Sin recuadro gris) -->
            <div class="flex justify-end items-center mb-2 sm:mb-2.5">
                <a href="{{ route('redes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-md text-[11px] sm:text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1 cursor-pointer">
                    + Nueva Red
                </a>
            </div>

            <!-- Tabla de Redes con Scroll Interno Controlado -->
            <div class="border rounded-lg max-h-[calc(100vh-200px)] overflow-y-auto overflow-x-auto shadow-2xs w-full">
                <table class="w-full divide-y divide-gray-200 border-collapse">
                    <thead class="bg-gray-100 sticky top-0 z-20">
                        <tr>
                            <th class="text-left font-medium text-gray-600 uppercase tracking-wider">Nombre</th>
                            <th class="solo-escritorio text-left font-medium text-gray-600 uppercase tracking-wider">Descripción</th>
                            <th class="text-center font-medium text-gray-600 uppercase tracking-wider">Horarios</th>
                            <th class="solo-escritorio text-center font-medium text-gray-600 uppercase tracking-wider">Tramos</th>
                            <th class="text-center font-medium text-gray-600 uppercase tracking-wider">Tomas</th>
                            <th class="text-right font-medium text-gray-600 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($redes as $red)
                            <tr class="hover:bg-gray-50">
                                <td class="font-bold text-gray-900">
                                    {{ $red->nombre }}
                                </td>
                                <td class="solo-escritorio text-gray-500">
                                    {{ $red->descripcion ?? 'Sin descripción' }}
                                </td>
                                <td class="text-center whitespace-nowrap">
                                    <span class="badge-fluido inline-flex items-center justify-center leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $red->horarios_count }}
                                    </span>
                                </td>
                                <td class="solo-escritorio text-center whitespace-nowrap">
                                    <span class="badge-fluido inline-flex items-center justify-center leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $red->tramos_count }}
                                    </span>
                                </td>
                                <td class="text-center whitespace-nowrap">
                                    <span class="badge-fluido inline-flex items-center justify-center leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $red->usuarios_count }}
                                    </span>
                                </td>
                                <td class="text-right font-medium whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        <!-- Ver -->
                                        <button type="button"
                                            title="Ver red en mapa"
                                            aria-label="Ver red en mapa"
                                            class="btn-ver-red btn-accion-icono rounded-md bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-900 transition cursor-pointer"
                                            data-nombre="{{ $red->nombre }}"
                                            data-tramos="{{ json_encode($red->tramos) }}">
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <!-- Editar -->
                                        <a href="{{ route('redes.edit', $red) }}"
                                            title="Editar red"
                                            aria-label="Editar red"
                                            class="btn-accion-icono rounded-md bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-900 transition">
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <!-- Eliminar -->
                                        <form action="{{ route('redes.destroy', $red) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta red?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                title="Eliminar red"
                                                aria-label="Eliminar red"
                                                class="btn-accion-icono rounded-md bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-900 transition cursor-pointer">
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-400">
                                    No hay redes registradas todavía. ¡Crea una nueva!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- MODAL VISTA RÁPIDA DE LA RED (SOLO TRAMOS/TUBERÍAS) -->
    <div id="modal-red" onclick="if(event.target === this) cerrarModalRed()" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-60 flex items-center justify-center p-3 sm:p-4" style="display: none;">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-full sm:max-w-2xl w-full p-3">
            <div class="flex justify-between items-center pb-2 mb-2 border-b">
                <h3 class="text-xs sm:text-sm font-bold text-gray-800 truncate pr-2">
                    Red: <span id="modal-red-nombre" class="text-blue-600"></span>
                </h3>
                <button type="button" onclick="cerrarModalRed()" class="text-gray-400 hover:text-gray-700 font-bold text-xl leading-none cursor-pointer">&times;</button>
            </div>

            <!-- Contenedor Único del Mapa -->
            <div id="mapa-red-detalle" class="w-full h-[280px] sm:h-[400px] rounded-md border shadow-inner"></div>
        </div>
    </div>

    <!-- JS de Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        let mapaRedModal = null;
        let redLayerGroup = null;

        document.addEventListener("DOMContentLoaded", function () {
            mapaRedModal = L.map('mapa-red-detalle').setView([25.3263827, -103.295072], 15);
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: 'Esri'
            }).addTo(mapaRedModal);

            redLayerGroup = L.layerGroup().addTo(mapaRedModal);

            document.querySelectorAll('.btn-ver-red').forEach(button => {
                button.addEventListener('click', function () {
                    const nombreRed = this.getAttribute('data-nombre');
                    let tramos = [];
                    try {
                        tramos = JSON.parse(this.getAttribute('data-tramos'));
                    } catch (e) {
                        tramos = [];
                    }

                    abrirModalRed(nombreRed, tramos);
                });
            });
        });

        function abrirModalRed(nombreRed, tramos) {
            document.getElementById('modal-red-nombre').innerText = nombreRed;
            document.getElementById('modal-red').style.display = 'flex';

            setTimeout(() => {
                mapaRedModal.invalidateSize();
                redLayerGroup.clearLayers();

                var bounds = L.featureGroup();

                if (tramos && tramos.length > 0) {
                    tramos.forEach(tramo => {
                        if (tramo.coordenadas_linea && Array.isArray(tramo.coordenadas_linea)) {
                            var latLngs = tramo.coordenadas_linea.map(coord => [coord.lat, coord.lng]);
                            if (latLngs.length > 0) {
                                var polyline = L.polyline(latLngs, {
                                    color: '#06b6d4',
                                    weight: 6,
                                    opacity: 0.9
                                }).bindPopup(`<b>Tramo:</b> ${tramo.nombre_tramo || 'Sin nombre'} (${tramo.diametro_pulgadas || 0}")`);
                                
                                redLayerGroup.addLayer(polyline);
                                bounds.addLayer(polyline);
                            }
                        }
                    });
                }

                if (bounds.getLayers().length > 0) {
                    mapaRedModal.fitBounds(bounds.getBounds(), { padding: [40, 40] });
                } else {
                    mapaRedModal.setView([25.3263827, -103.295072], 15);
                }
            }, 150);
        }

        function cerrarModalRed() {
            document.getElementById('modal-red').style.display = 'none';
        }
    </script>
</x-app-layout>