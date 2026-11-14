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

    <!-- CONTENEDOR DE ANCHO COMPLETO Y DINÁMICO -->
    <div class="py-2 px-2 sm:px-3 w-full">
        <div class="bg-white overflow-hidden shadow-2xs rounded-lg p-3 w-full">
            
            @if(session('success'))
                <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-3 py-1.5 rounded text-xs" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- BOTÓN NUEVA RED ALINEADO A LA DERECHA (Sin recuadro gris) -->
            <div class="flex justify-end items-center mb-2.5">
                <a href="{{ route('redes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-1.5 rounded-md text-[11px] sm:text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1 cursor-pointer">
                    + Nueva Red
                </a>
            </div>

            <!-- Tabla de Redes con Scroll Interno Controlado -->
            <div class="border rounded-lg max-h-[calc(100vh-210px)] overflow-y-auto overflow-x-auto shadow-2xs">
                <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-xs lg:text-[13.5px] border-collapse">
                    <thead class="bg-gray-100 sticky top-0 z-20">
                        <tr>
                            <th class="px-3 py-2.5 text-left font-medium text-gray-600 uppercase tracking-wider w-1/5 text-xs sm:text-xs lg:text-xs">Nombre de la Red</th>
                            <th class="px-3 py-2.5 text-left font-medium text-gray-600 uppercase tracking-wider w-2/5 text-xs sm:text-xs lg:text-xs">Descripción</th>
                            <th class="px-2 py-2.5 text-center font-medium text-gray-600 uppercase tracking-wider text-xs sm:text-xs lg:text-xs">Horarios</th>
                            <th class="px-2 py-2.5 text-center font-medium text-gray-600 uppercase tracking-wider text-xs sm:text-xs lg:text-xs">Tramos</th>
                            <th class="px-2 py-2.5 text-center font-medium text-gray-600 uppercase tracking-wider text-xs sm:text-xs lg:text-xs">Tomas</th>
                            <th class="px-3 py-2.5 text-right font-medium text-gray-600 uppercase tracking-wider text-xs sm:text-xs lg:text-xs">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($redes as $red)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2.5 font-bold text-gray-900 text-xs sm:text-xs lg:text-[13.5px]">
                                    {{ $red->nombre }}
                                </td>
                                <td class="px-3 py-2.5 text-gray-500 text-xs sm:text-xs lg:text-[13.5px]">
                                    {{ $red->descripcion ?? 'Sin descripción' }}
                                </td>
                                <td class="px-2 py-2.5 text-center text-gray-500">
                                    <span class="px-2 py-0.5 inline-flex text-[10px] sm:text-[11px] lg:text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $red->horarios_count }}
                                    </span>
                                </td>
                                <td class="px-2 py-2.5 text-center text-gray-500">
                                    <span class="px-2 py-0.5 inline-flex text-[10px] sm:text-[11px] lg:text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $red->tramos_count }}
                                    </span>
                                </td>
                                <td class="px-2 py-2.5 text-center text-gray-500">
                                    <span class="px-2 py-0.5 inline-flex text-[10px] sm:text-[11px] lg:text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $red->usuarios_count }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-right font-medium whitespace-nowrap text-xs sm:text-xs lg:text-[13.5px]">
                                    <button type="button" 
                                        class="btn-ver-red text-indigo-600 hover:text-indigo-900 mr-2 font-semibold cursor-pointer"
                                        data-nombre="{{ $red->nombre }}"
                                        data-tramos="{{ json_encode($red->tramos) }}">
                                        Ver
                                    </button>

                                    <a href="{{ route('redes.edit', $red) }}" class="text-yellow-600 hover:text-yellow-900 mr-2 font-semibold">Editar</a>
                                    <form action="{{ route('redes.destroy', $red) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta red?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold cursor-pointer">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-gray-400">
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
    <div id="modal-red" onclick="if(event.target === this) cerrarModalRed()" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-60 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-2xl sm:w-full p-3">
            <div class="flex justify-between items-center pb-2 mb-2 border-b">
                <h3 class="text-sm font-bold text-gray-800 truncate pr-2">
                    Red: <span id="modal-red-nombre" class="text-blue-600"></span>
                </h3>
                <button type="button" onclick="cerrarModalRed()" class="text-gray-400 hover:text-gray-700 font-bold text-xl leading-none cursor-pointer">&times;</button>
            </div>

            <!-- Contenedor Único del Mapa -->
            <div id="mapa-red-detalle" style="height: 400px;" class="w-full rounded-md border shadow-inner"></div>
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