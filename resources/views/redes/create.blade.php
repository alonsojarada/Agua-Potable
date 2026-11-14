<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center min-w-0">
            <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight truncate min-w-0" title="Registrar Nueva Red y Trazo de Tubería">
                {{ __('Registrar Nueva Red y Trazo de Tubería') }}
            </h2>
        </div>
    </x-slot>

    <!-- Cargar CSS de Leaflet y Leaflet.draw -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>

    <!-- CONTENEDOR ADAPTATIVO -->
    <div class="py-2 px-2 sm:px-3 w-full">
        <div class="bg-transparent overflow-hidden rounded-lg p-0 w-full h-auto lg:h-[calc(100vh-125px)] flex flex-col">
            
            @if ($errors->any())
                <div class="mb-2 bg-red-100 border border-red-400 text-red-700 px-3 py-1 rounded text-xs shrink-0">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('redes.store') }}" method="POST" id="red-form" class="flex-1 flex flex-col min-h-0">
                @csrf

                <!-- DISEÑO RESPONSIVO: Formulario compacto en grandes (3 de 12), cómodo en medianas (4 de 12), y apilado en celulares -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 h-auto lg:h-full min-h-0">
                    
                    <!-- ================= COLUMNA IZQUIERDA: FORMULARIO COMPACTO ================= -->
                    <div class="lg:col-span-4 xl:col-span-3 bg-gray-50 border border-gray-200 rounded-lg p-3.5 shadow-2xs flex flex-col h-auto lg:h-full overflow-y-auto">
                        <div class="space-y-3">
                            <h3 class="font-bold text-gray-800 text-xs sm:text-sm mb-1 uppercase">Datos de la Red</h3>

                            <div>
                                <label for="nombre" class="block text-xs font-medium text-gray-700 mb-1">Nombre de la Red / Circuito</label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                                    class="w-full rounded-md border-gray-300 shadow-2xs text-xs sm:text-sm py-2 px-3 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Ej: Circuito Sector Norte">
                            </div>

                            <div>
                                <label for="nombre_tramo" class="block text-xs font-medium text-gray-700 mb-1">Nombre del Tramo Inicial</label>
                                <input type="text" name="nombre_tramo" id="nombre_tramo" value="{{ old('nombre_tramo', 'Red Principal') }}" required
                                    class="w-full rounded-md border-gray-300 shadow-2xs text-xs sm:text-sm py-2 px-3 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Ej: Línea Principal Calle Hidalgo">
                            </div>

                            <div>
                                <label for="diametro_pulgadas" class="block text-xs font-medium text-gray-700 mb-1">Diámetro de Tubería (Pulgadas)</label>
                                <input type="number" step="0.01" name="diametro_pulgadas" id="diametro_pulgadas" value="2.00" required
                                    class="w-full rounded-md border-gray-300 shadow-2xs text-xs sm:text-sm py-2 px-3 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="descripcion" class="block text-xs font-medium text-gray-700 mb-1">Descripción / Observaciones</label>
                                <textarea name="descripcion" id="descripcion" rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-2xs text-xs sm:text-sm py-2 px-3 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Detalles de la infraestructura...">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>

                        <!-- Campo oculto donde se guardará el JSON con las coordenadas -->
                        <input type="hidden" name="coordenadas_linea" id="coordenadas_linea" required>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-200">
                            <a href="{{ route('redes.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-xs sm:text-sm font-semibold shadow-2xs transition inline-flex items-center justify-center cursor-pointer">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-xs sm:text-sm font-semibold shadow-2xs transition cursor-pointer">
                                Guardar Red
                            </button>
                        </div>
                    </div>

                    <!-- ================= COLUMNA DERECHA: MAPA EXPANDIDO ================= -->
                    <div class="lg:col-span-8 xl:col-span-9 bg-white border border-gray-200 rounded-lg p-3 shadow-2xs flex flex-col h-[450px] lg:h-full">
                        <div class="flex justify-between items-center mb-2 shrink-0">
                            <label class="block text-[11px] sm:text-xs font-bold text-gray-700">Trazo de la Red en el Mapa (Usa la herramienta de línea para marcar los puntos de la tubería)</label>
                            <button type="button" id="clear-map" class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded hover:bg-red-200 font-semibold cursor-pointer">Reiniciar Trazo</button>
                        </div>
                        
                        <!-- Mapa interactivo -->
                        <div id="map-tramos" class="w-full flex-1 rounded-md border border-gray-300 shadow-inner z-0"></div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- Cargar JS de Leaflet y Leaflet.draw -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicializar el mapa centrado en tus coordenadas base
        var map = L.map('map-tramos').setView([25.3263827, -103.295072], 16);

        // Cambiar la capa base por IMÁGENES SATELITALES (Esri World Imagery)
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri'
        }).addTo(map);

        // Capa para almacenar las figuras dibujadas
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        // Configurar los controles de dibujo (solo permitiremos Polylines / Líneas de tubería)
        var drawControl = new L.Control.Draw({
            position: 'topright',
            draw: {
                polygon: false,
                rectangle: false,
                circle: false,
                circlemarker: false,
                marker: false,
                polyline: {
                    shapeOptions: {
                        color: '#2563eb', // Azul fuerte para la tubería
                        weight: 5
                    }
                }
            },
            edit: {
                featureGroup: drawnItems,
                edit: true,
                remove: true
            }
        });
        map.addControl(drawControl);

        // Evento cuando se crea una nueva línea (trazo de la red)
        map.on(L.Draw.Event.CREATED, function (e) {
            drawnItems.clearLayers();
            
            var layer = e.layer;
            drawnItems.addLayer(layer);

            var latlngs = layer.getLatLngs();
            
            var coordsArray = latlngs.map(function(latlng) {
                return { lat: latlng.lat, lng: latlng.lng };
            });

            document.getElementById('coordenadas_linea').value = JSON.stringify(coordsArray);
        });

        // Evento cuando se edita un trazo existente
        map.on(L.Draw.Event.EDITED, function (e) {
            var layers = e.layers;
            layers.eachLayer(function (layer) {
                var latlngs = layer.getLatLngs();
                var coordsArray = latlngs.map(function(latlng) {
                    return { lat: latlng.lat, lng: latlng.lng };
                });
                document.getElementById('coordenadas_linea').value = JSON.stringify(coordsArray);
            });
        });

        // Botón para limpiar el mapa y reiniciar el trazo
        document.getElementById('clear-map').addEventListener('click', function() {
            drawnItems.clearLayers();
            document.getElementById('coordenadas_linea').value = '';
        });
        
        // Ajustar tamaño del mapa tras renderizar
        setTimeout(() => { map.invalidateSize(); }, 250);
    });
</script>
</x-app-layout>