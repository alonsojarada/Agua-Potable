<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Red de Agua y Trazo de Tubería') }}
        </h2>
    </x-slot>

    <!-- Cargar CSS de Leaflet y Leaflet.draw -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('redes.update', $rede) }}" method="POST" id="red-form">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Red /
                                    Circuito</label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $rede->nombre) }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="nombre_tramo" class="block text-sm font-medium text-gray-700">Nombre del
                                    Tramo</label>
                                <input type="text" name="nombre_tramo" id="nombre_tramo"
                                    value="{{ old('nombre_tramo', $tramoInicial->nombre_tramo ?? 'Red Principal') }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción /
                                Observaciones</label>
                            <textarea name="descripcion" id="descripcion" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('descripcion', $rede->descripcion) }}</textarea>
                        </div>

                        <!-- Mapa interactivo -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-medium text-gray-700">Trazo de la Red (Usa el menú de
                                    herramientas para editar o continuar la línea)</label>
                                <button type="button" id="clear-map"
                                    class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200 font-semibold">Reiniciar
                                    Trazo</button>
                            </div>
                            <div id="map-tramos" style="height: 450px;" class="rounded-lg border border-gray-300 z-0">
                            </div>
                        </div>

                        <!-- Campo oculto para las coordenadas en JSON -->
                        <input type="hidden" name="coordenadas_linea" id="coordenadas_linea"
                            value="{{ json_encode($tramoInicial->coordenadas_linea ?? []) }}" required>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="diametro_pulgadas" class="block text-sm font-medium text-gray-700">Diámetro
                                    de Tubería (Pulgadas)</label>
                                <input type="number" step="0.01" name="diametro_pulgadas" id="diametro_pulgadas"
                                    value="{{ old('diametro_pulgadas', $tramoInicial->diametro_pulgadas ?? 2.00) }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('redes.index') }}"
                                class="text-gray-600 hover:text-gray-900 mr-4 text-sm font-semibold">Cancelar</a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-semibold shadow">
                                Actualizar Red y Trazo
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Cargar JS de Leaflet y Leaflet.draw -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Coordenadas existentes desde la base de datos
            var existingCoords = @json($tramoInicial->coordenadas_linea ?? []);

            var centerLat = (existingCoords.length > 0) ? existingCoords[0].lat : 25.3263827;
            var centerLng = (existingCoords.length > 0) ? existingCoords[0].lng : -103.295072;

            var map = L.map('map-tramos').setView([centerLat, centerLng], 14);

            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
            }).addTo(map);

            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            // Si ya hay coordenadas guardadas, dibujarlas y agregarlas al grupo editable
            if (existingCoords.length > 0) {
                var latLngs = existingCoords.map(coord => [coord.lat, coord.lng]);
                var existingPolyline = L.polyline(latLngs, { color: '#2563eb', weight: 5 });
                drawnItems.addLayer(existingPolyline);
                map.fitBounds(existingPolyline.getBounds());
            }

            // Configurar controles de dibujo (Leaflet.Draw)
            var drawControl = new L.Control.Draw({
                position: 'topright',
                draw: {
                    polygon: false, rectangle: false, circle: false, circlemarker: false, marker: false,
                    polyline: { shapeOptions: { color: '#2563eb', weight: 5 } }
                },
                edit: {
                    featureGroup: drawnItems,
                    edit: true, // Permite mover nodos y editar vértices existentes
                    remove: true
                }
            });
            map.addControl(drawControl);

            // Función auxiliar para guardar coordenadas en el input oculto
            function extractAndSaveCoords() {
                var allCoords = [];
                drawnItems.eachLayer(function (layer) {
                    if (layer instanceof L.Polyline) {
                        var latlngs = layer.getLatLngs();
                        // Si es un arreglo multidimensional (por multi-líneas), lo aplanamos o tomamos el principal
                        if (Array.isArray(latlngs) && Array.isArray(latlngs[0])) {
                            latlngs = latlngs[0];
                        }
                        latlngs.forEach(function (latlng) {
                            allCoords.push({ lat: latlng.lat, lng: latlng.lng });
                        });
                    }
                });
                document.getElementById('coordenadas_linea').value = JSON.stringify(allCoords);
            }

            // Eventos de Leaflet.Draw
            map.on(L.Draw.Event.CREATED, function (e) {
                // Si quieres que al crear un nuevo trazo con la herramienta se reemplace o se una:
                // Para conservar el anterior y sumar este nuevo, no limpiamos drawnItems, o si prefieres limpiar:
                drawnItems.clearLayers();
                drawnItems.addLayer(e.layer);
                extractAndSaveCoords();
            });

            map.on(L.Draw.Event.EDITED, function (e) {
                extractAndSaveCoords();
            });

            map.on(L.Draw.Event.DELETED, function (e) {
                extractAndSaveCoords();
            });

            // Botón para reiniciar todo el mapa
            document.getElementById('clear-map').addEventListener('click', function () {
                drawnItems.clearLayers();
                document.getElementById('coordenadas_linea').value = '';
            });
        });
    </script>
</x-app-layout>