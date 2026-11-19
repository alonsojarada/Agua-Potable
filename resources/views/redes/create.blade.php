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

    <style>
        .form-red-fluido {
            font-size: clamp(0.65rem, 1.25vw, 0.875rem);
        }
        .form-red-fluido .titulo-fluido {
            font-size: clamp(0.7rem, 1.35vw, 0.92rem);
        }
        .form-red-fluido .label-fluido {
            font-size: clamp(0.58rem, 1.05vw, 0.75rem);
        }
        .form-red-fluido .control-fluido {
            font-size: clamp(0.62rem, 1.15vw, 0.875rem);
            padding: clamp(0.22rem, 0.5vw, 0.5rem) clamp(0.35rem, 0.75vw, 0.75rem);
        }
        .form-red-fluido .btn-accion-fluido {
            font-size: clamp(0.62rem, 1.15vw, 0.875rem);
            padding: clamp(0.25rem, 0.55vw, 0.5rem) clamp(0.45rem, 0.9vw, 1rem);
        }
    </style>

    <!-- CONTENEDOR ADAPTATIVO -->
    <div class="py-1.5 sm:py-2 px-1.5 sm:px-3 w-full">
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
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-2.5 sm:gap-3 h-auto lg:h-full min-h-0">
                    
                    <!-- ================= COLUMNA IZQUIERDA: FORMULARIO COMPACTO (2 COLUMNAS SOLO EN CHICO/MÓVIL) ================= -->
                    <div class="lg:col-span-4 xl:col-span-3 bg-gray-50 border border-gray-200 rounded-lg p-2.5 sm:p-3.5 shadow-2xs flex flex-col justify-between h-auto lg:h-full overflow-x-auto overflow-y-auto form-red-fluido">
                        <div class="space-y-2 lg:space-y-3 min-w-[240px] lg:min-w-0">
                            <h3 class="font-bold text-gray-800 titulo-fluido mb-0.5 lg:mb-1 uppercase">Datos de la Red</h3>

                            <div class="grid grid-cols-2 lg:grid-cols-1 gap-2 lg:gap-3">
                                <div class="min-w-0">
                                    <label for="nombre" class="block label-fluido font-medium text-gray-700 mb-0.5 lg:mb-1 truncate">Nombre de la Red / Circuito</label>
                                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                                        class="w-full rounded-md border-gray-300 shadow-2xs control-fluido focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Ej: Circuito Sector Norte">
                                </div>

                                <div class="min-w-0">
                                    <label for="nombre_tramo" class="block label-fluido font-medium text-gray-700 mb-0.5 lg:mb-1 truncate">Nombre del Tramo Inicial</label>
                                    <input type="text" name="nombre_tramo" id="nombre_tramo" value="{{ old('nombre_tramo', 'Red Principal') }}" required
                                        class="w-full rounded-md border-gray-300 shadow-2xs control-fluido focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Ej: Línea Principal Calle Hidalgo">
                                </div>

                                <div class="min-w-0">
                                    <label for="diametro_pulgadas" class="block label-fluido font-medium text-gray-700 mb-0.5 lg:mb-1 truncate">Diámetro de Tubería (Pulgadas)</label>
                                    <input type="number" step="0.01" name="diametro_pulgadas" id="diametro_pulgadas" value="2.00" required
                                        class="w-full rounded-md border-gray-300 shadow-2xs control-fluido focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div class="min-w-0">
                                    <label for="descripcion" class="block label-fluido font-medium text-gray-700 mb-0.5 lg:mb-1 truncate">Descripción / Observaciones</label>
                                    <textarea name="descripcion" id="descripcion" rows="2"
                                        class="w-full rounded-md border-gray-300 shadow-2xs control-fluido focus:border-blue-500 focus:ring-blue-500 lg:rows-3"
                                        placeholder="Detalles de la infraestructura...">{{ old('descripcion') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Campo oculto donde se guardará el JSON con las coordenadas -->
                        <input type="hidden" name="coordenadas_linea" id="coordenadas_linea" required>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-between gap-2 pt-2.5 lg:pt-4 mt-2.5 lg:mt-4 border-t border-gray-200 shrink-0 min-w-[240px] lg:min-w-0">
                            <a href="{{ route('redes.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 btn-accion-fluido rounded-md font-semibold shadow-2xs transition inline-flex items-center justify-center cursor-pointer whitespace-nowrap">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white btn-accion-fluido rounded-md font-semibold shadow-2xs transition cursor-pointer whitespace-nowrap">
                                Guardar Red
                            </button>
                        </div>
                    </div>

                    <!-- ================= COLUMNA DERECHA: MAPA EXPANDIDO ================= -->
                    <div class="lg:col-span-8 xl:col-span-9 bg-white border border-gray-200 rounded-lg p-2.5 sm:p-3 shadow-2xs flex flex-col h-[420px] sm:h-[450px] lg:h-full overflow-x-auto">
                        <div class="flex justify-between items-center gap-2 mb-2 shrink-0 min-w-[300px] sm:min-w-0">
                            <label class="block text-[11px] sm:text-xs font-bold text-gray-700">Trazo de la Red en el Mapa (Usa la herramienta de línea para marcar los puntos de la tubería)</label>
                            <button type="button" id="clear-map" class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded hover:bg-red-200 font-semibold cursor-pointer shrink-0">Reiniciar Trazo</button>
                        </div>
                        
                        <!-- Mapa interactivo con scroll horizontal si la ventana es muy chica -->
                        <div id="map-tramos" class="w-full min-w-[300px] sm:min-w-0 flex-1 rounded-md border border-gray-300 shadow-inner z-0"></div>
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
        // 1. Definir las capas base disponibles
        var capaEsri = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri'
        });

        var capaGoogleSat = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        });

        var capaGoogleRoad = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        });

        // 2. Inicializar el mapa centrado (usando Esri por defecto)
        var map = L.map('map-tramos', {
            layers: [capaEsri]
        }).setView([25.3263827, -103.295072], 16);

        // 3. Crear control personalizado de botón Esri y select de Google para cambiar de mapa
        var SelectorCapas = L.Control.extend({
            options: { position: 'topright' },

            onAdd: function (map) {
                var contenedor = L.DomUtil.create('div', 'leaflet-bar leaflet-control bg-white shadow-md rounded-md p-1 flex items-center gap-1 text-xs font-semibold z-[999]');
                
                contenedor.innerHTML = `
                    <button type="button" id="btn-esri" class="px-2.5 py-1 rounded bg-blue-600 text-white cursor-pointer transition whitespace-nowrap">Esri / Sat</button>
                    <select id="select-google-capa" aria-label="Capas de Google" class="px-2 py-1 rounded border border-gray-300 bg-gray-100 text-gray-700 text-xs font-semibold cursor-pointer focus:outline-none focus:border-blue-500">
                        <option value="" selected disabled>Google...</option>
                        <option value="google-sat">Google Sat</option>
                        <option value="google-road">Google Calles</option>
                    </select>
                `;

                L.DomEvent.disableClickPropagation(contenedor);
                return contenedor;
            }
        });

        map.addControl(new SelectorCapas());

        var btnEsri = document.getElementById('btn-esri');
        var selectGoogle = document.getElementById('select-google-capa');

        function cambiarCapaBase(nuevaCapa) {
            map.eachLayer(layer => {
                if (layer instanceof L.TileLayer) {
                    map.removeLayer(layer);
                }
            });
            nuevaCapa.addTo(map);
        }

        // 4. Lógica de eventos para alternar las capas del mapa
        btnEsri.addEventListener('click', function () {
            cambiarCapaBase(capaEsri);
            btnEsri.className = "px-2.5 py-1 rounded bg-blue-600 text-white cursor-pointer transition whitespace-nowrap";
            selectGoogle.value = "";
            selectGoogle.className = "px-2 py-1 rounded border border-gray-300 bg-gray-100 text-gray-700 text-xs font-semibold cursor-pointer focus:outline-none focus:border-blue-500";
        });

        selectGoogle.addEventListener('change', function () {
            if (this.value === 'google-sat') {
                cambiarCapaBase(capaGoogleSat);
            } else if (this.value === 'google-road') {
                cambiarCapaBase(capaGoogleRoad);
            }

            if (this.value) {
                btnEsri.className = "px-2.5 py-1 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 cursor-pointer transition whitespace-nowrap";
                this.className = "px-2 py-1 rounded border border-blue-600 bg-blue-600 text-white text-xs font-semibold cursor-pointer focus:outline-none";
            }
        });

        // Capa para almacenar las figuras dibujadas (Tuberías)
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        // Configurar los controles de dibujo (Polylines / Líneas de tubería)
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