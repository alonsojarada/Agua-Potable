<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center min-w-0">
            <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight truncate min-w-0" title="Registrar Nueva Toma de Agua">
                {{ __('Registrar Nueva Toma de Agua') }}
            </h2>
        </div>
    </x-slot>

    <!-- Cargar CSS de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- CONTENEDOR ADAPTATIVO -->
    <div class="py-1.5 sm:py-2 px-2 sm:px-3 w-full">
        <div class="bg-transparent overflow-visible lg:overflow-hidden rounded-lg p-0 w-full h-auto lg:h-[calc(100vh-125px)] flex flex-col">

            @if ($errors->any())
                <div class="mb-2 bg-red-100 border border-red-400 text-red-700 px-3 py-1 rounded text-xs shrink-0">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('usuarios.store') }}" method="POST" class="flex-1 flex flex-col min-h-0">
                @csrf

                <!-- DISEÑO RESPONSIVO: Dos columnas simétricas -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-2 sm:gap-3 h-auto lg:h-full min-h-0">
                    
                    <!-- ================= COLUMNA IZQUIERDA: FORMULARIO ================= -->
                    <div class="lg:col-span-5 xl:col-span-4 bg-gray-50 border border-gray-200 rounded-lg p-2 sm:p-3.5 shadow-2xs flex flex-col h-auto lg:h-full overflow-y-auto">
                        <div class="space-y-1 sm:space-y-3 flex-1">
                            <h3 class="font-bold text-gray-800 text-[10px] sm:text-sm mb-0.5 sm:mb-1 uppercase">Datos de la Toma</h3>

                            <!-- Red y Estado en 2 columnas solo en móvil; apilados desde sm: -->
                            <div class="grid grid-cols-2 sm:grid-cols-1 gap-1.5 sm:gap-3">
                                <!-- Red o Circuito -->
                                <div>
                                    <label for="red_id" class="block text-[10px] sm:text-xs font-medium text-gray-700 mb-0.5 sm:mb-1">Red o Circuito Asignado</label>
                                    <select name="red_id" id="red_id" required
                                        class="w-full rounded-md border-gray-300 shadow-2xs text-[11px] sm:text-sm py-1 sm:py-2 px-2 sm:px-3 focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Seleccione una red...</option>
                                        @foreach($redes as $red)
                                            <option value="{{ $red->id }}" {{ old('red_id') == $red->id ? 'selected' : '' }}>
                                                {{ $red->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Estado del Servicio -->
                                <div>
                                    <label for="estado_servicio" class="block text-[10px] sm:text-xs font-medium text-gray-700 mb-0.5 sm:mb-1">Estado del Servicio</label>
                                    <select name="estado_servicio" id="estado_servicio" required
                                        class="w-full rounded-md border-gray-300 shadow-2xs text-[11px] sm:text-sm py-1 sm:py-2 px-2 sm:px-3 focus:border-blue-500 focus:ring-blue-500">
                                        <option value="activo">Activo</option>
                                        <option value="suspendido">Suspendido</option>
                                        <option value="cortado">Cortado</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tarifa Mensual con Botón -->
                            <div>
                                <label for="tarifa_id" class="block text-[10px] sm:text-xs font-medium text-gray-700 mb-0.5 sm:mb-1">Tarifa Mensual Asignada</label>
                                <div class="flex items-center gap-1.5 sm:gap-2">
                                    <select name="tarifa_id" id="select-tarifa" required
                                        class="w-full rounded-md border-gray-300 shadow-2xs text-[11px] sm:text-sm py-1 sm:py-2 px-2 sm:px-3 focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Seleccione una tarifa...</option>
                                        @foreach($tarifas as $tarifa)
                                            @if($tarifa->estatus)
                                                <option value="{{ $tarifa->id }}" {{ old('tarifa_id') == $tarifa->id ? 'selected' : '' }}>
                                                    {{ $tarifa->nombre }} (${{ number_format($tarifa->monto_mensual, 2) }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="abrirModalTarifa()"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 sm:px-3 py-1 sm:py-2 rounded-md text-[11px] sm:text-xs font-semibold shadow-2xs shrink-0 cursor-pointer">
                                        + Nueva
                                    </button>
                                </div>
                            </div>

                            <!-- Nombre del Titular -->
                            <div>
                                <label for="nombre_completo" class="block text-[10px] sm:text-xs font-medium text-gray-700 mb-0.5 sm:mb-1">Nombre del Titular</label>
                                <input type="text" name="nombre_completo" id="nombre_completo" value="{{ old('nombre_completo') }}" required
                                    class="w-full rounded-md border-gray-300 shadow-2xs text-[11px] sm:text-sm py-1 sm:py-2 px-2 sm:px-3 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- Teléfono y Dirección en 2 columnas solo en móvil; apilados desde sm: -->
                            <div class="grid grid-cols-2 sm:grid-cols-1 gap-1.5 sm:gap-3">
                                <!-- Teléfono -->
                                <div>
                                    <label for="telefono" class="block text-[10px] sm:text-xs font-medium text-gray-700 mb-0.5 sm:mb-1">Teléfono <span class="text-gray-400 font-normal">(Opcional)</span></label>
                                    <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                                        class="w-full rounded-md border-gray-300 shadow-2xs text-[11px] sm:text-sm py-1 sm:py-2 px-2 sm:px-3 focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <!-- Dirección -->
                                <div>
                                    <label for="direccion" class="block text-[10px] sm:text-xs font-medium text-gray-700 mb-0.5 sm:mb-1">Dirección del Predio <span class="text-gray-400 font-normal">(Opcional)</span></label>
                                    <input type="text" name="direccion" id="direccion" value="{{ old('direccion') }}"
                                        class="w-full rounded-md border-gray-300 shadow-2xs text-[11px] sm:text-sm py-1 sm:py-2 px-2 sm:px-3 focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Calle, Número, Colonia...">
                                </div>
                            </div>

                            <!-- Coordenadas Ocultas / Resumen -->
                            <div class="grid grid-cols-2 gap-1.5 sm:gap-2 pt-0.5 sm:pt-1">
                                <div>
                                    <label for="latitud" class="block text-[9px] sm:text-[10px] font-medium text-gray-500">Latitud</label>
                                    <input type="text" name="latitud" id="latitud" value="{{ old('latitud', '25.3263827') }}" required readonly
                                        class="w-full rounded-md border-gray-300 bg-gray-100 text-[10px] sm:text-xs py-0.5 sm:py-1 px-1.5 sm:px-2">
                                </div>
                                <div>
                                    <label for="longitud" class="block text-[9px] sm:text-[10px] font-medium text-gray-500">Longitud</label>
                                    <input type="text" name="longitud" id="longitud" value="{{ old('longitud', '-103.295072') }}" required readonly
                                        class="w-full rounded-md border-gray-300 bg-gray-100 text-[10px] sm:text-xs py-0.5 sm:py-1 px-1.5 sm:px-2">
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center gap-2 pt-1.5 mt-1.5 sm:pt-4 sm:mt-4 border-t border-gray-200 shrink-0">
                            <a href="{{ route('usuarios.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-1 sm:py-2 px-3 rounded-md text-xs sm:text-sm font-semibold shadow-2xs transition text-center cursor-pointer">
                                Cancelar
                            </a>
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-1 sm:py-2 px-3 rounded-md text-xs sm:text-sm font-semibold shadow-2xs transition text-center cursor-pointer">
                                Guardar
                            </button>
                        </div>
                    </div>

                    <!-- ================= COLUMNA DERECHA: MAPA EXPANDIDO ================= -->
                    <div class="lg:col-span-7 xl:col-span-8 bg-white border border-gray-200 rounded-lg p-2 sm:p-3 shadow-2xs flex flex-col h-[380px] sm:h-[450px] lg:h-full">
                        <div class="flex flex-wrap justify-between items-center gap-1.5 mb-1 sm:mb-2 shrink-0">
                            <label class="block text-[10px] sm:text-xs font-bold text-gray-700">Ubicación de la Toma (Haz clic o arrastra el marcador)</label>

                            <div class="flex items-center gap-1">
                                <select id="tipo-mapa" onchange="cambiarCapaMapa(this.value)"
                                    class="rounded-md border-gray-300 text-[10px] sm:text-xs py-0.5 sm:py-1 px-2 bg-gray-50 text-gray-700 font-medium focus:border-blue-500 focus:ring-blue-500 cursor-pointer">
                                    <option value="satelite">Satélite (Esri)</option>
                                    <option value="google_maps">Google Maps</option>
                                    <option value="google_hibrido">Google Híbrido</option>
                                    <option value="google_satelite">Google Satélite</option>
                                    <option value="osm">OpenStreetMap</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Mapa interactivo -->
                        <div class="w-full flex-1 min-h-[320px] sm:min-h-0 rounded-md border border-gray-300 shadow-inner overflow-hidden relative z-0">
                            <div id="map-picker" class="w-full h-full"></div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- MODAL DE GESTIÓN DE TARIFAS -->
    <div id="modal-tarifa" onclick="if(event.target === this) cerrarModalTarifa()"
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-60 flex items-center justify-center p-4"
        style="display: none;">
        <div class="bg-white rounded-lg shadow-xl sm:max-w-xl sm:w-full p-5">

            <div class="flex justify-between items-center pb-3 border-b mb-3">
                <h3 class="text-base font-bold text-gray-800">Gestión de Tarifas</h3>
                <button type="button" onclick="cerrarModalTarifa()"
                    class="text-gray-400 hover:text-gray-700 font-bold text-xl leading-none cursor-pointer">&times;</button>
            </div>

            <form id="form-tarifa-ajax" class="flex gap-2 items-end mb-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Nombre</label>
                    <input type="text" id="nueva-nombre"
                        class="w-full rounded-md border-gray-300 shadow-sm sm:text-sm py-1.5" placeholder="Ej. Especial"
                        required>
                </div>
                <div class="w-32">
                    <label class="block text-xs text-gray-500 mb-1">Monto ($)</label>
                    <input type="number" step="0.01" id="nuevo-monto"
                        class="w-full rounded-md border-gray-300 shadow-sm sm:text-sm py-1.5" placeholder="0.00"
                        required>
                </div>
                <div>
                    <button type="button" onclick="guardarTarifaAjax()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-xs font-semibold shadow cursor-pointer">
                        + Agregar
                    </button>
                </div>
            </form>

            <div class="max-h-52 overflow-y-auto border rounded-md">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Estatus</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-tarifas-cuerpo" class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-4 pt-2 border-t">
                <button type="button" onclick="cerrarModalTarifa()"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-1.5 rounded-md text-xs font-semibold cursor-pointer">
                    Listo / Actualizar
                </button>
            </div>

        </div>
    </div>

    <!-- Cargar JS de Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        var map;
        var layersMapa = {};

        function cambiarCapaMapa(tipo) {
            if (!map) return;

            Object.values(layersMapa).forEach(layer => {
                if (layer && map.hasLayer(layer)) {
                    map.removeLayer(layer);
                }
            });

            if (layersMapa[tipo]) {
                layersMapa[tipo].addTo(map);
            } else if (layersMapa.satelite) {
                layersMapa.satelite.addTo(map);
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            var initialLat = parseFloat(document.getElementById('latitud').value) || 25.3263827;
            var initialLng = parseFloat(document.getElementById('longitud').value) || -103.295072;

            map = L.map('map-picker').setView([initialLat, initialLng], 17);

            layersMapa.satelite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 20,
                attribution: 'Tiles &copy; Esri'
            });

            layersMapa.google_maps = L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                attribution: '&copy; Google Maps'
            });

            layersMapa.google_hibrido = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                attribution: '&copy; Google Maps'
            });

            layersMapa.google_satelite = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                attribution: '&copy; Google Maps'
            });

            layersMapa.osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            });

            layersMapa.satelite.addTo(map);

            var marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

            function updateInputs(lat, lng) {
                document.getElementById('latitud').value = lat.toFixed(7);
                document.getElementById('longitud').value = lng.toFixed(7);
            }

            map.on('click', function (e) {
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;
                marker.setLatLng([lat, lng]);
                updateInputs(lat, lng);
            });

            marker.on('dragend', function (e) {
                var position = marker.getLatLng();
                updateInputs(position.lat, position.lng);
            });

            const mapEl = document.getElementById('map-picker');
            if (window.ResizeObserver && mapEl) {
                const ro = new ResizeObserver(() => map.invalidateSize());
                ro.observe(mapEl);
            }
            setTimeout(() => { map.invalidateSize(); }, 200);
            window.addEventListener('resize', () => { map.invalidateSize(); });
        });

        function abrirModalTarifa() {
            document.getElementById('modal-tarifa').style.display = 'flex';
            cargarTarifasTabla();
        }

        function cerrarModalTarifa() {
            document.getElementById('modal-tarifa').style.display = 'none';
            document.getElementById('form-tarifa-ajax').reset();
            actualizarSelectPrincipal();
        }

        function cargarTarifasTabla() {
            fetch("{{ route('tarifas.json') }}", {
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('tabla-tarifas-cuerpo');
                tbody.innerHTML = '';

                data.forEach(tarifa => {
                    const esActivo = Boolean(tarifa.estatus);
                    const badge = esActivo 
                        ? '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activa</span>' 
                        : '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Baja</span>';
                    const btnText = esActivo ? 'Dar de baja' : 'Activar';
                    const btnClass = esActivo ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-3 py-2 font-medium text-gray-900">${tarifa.nombre}</td>
                        <td class="px-3 py-2 text-gray-600">$${parseFloat(tarifa.monto_mensual).toFixed(2)}</td>
                        <td class="px-3 py-2 text-center">${badge}</td>
                        <td class="px-3 py-2 text-right">
                            <button type="button" onclick="toggleStatusTarifa(${tarifa.id})" class="px-2.5 py-1 rounded text-xs font-semibold ${btnClass} transition cursor-pointer">
                                ${btnText}
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });
        }

        function guardarTarifaAjax() {
            const nombre = document.getElementById('nueva-nombre').value;
            const monto = document.getElementById('nuevo-monto').value;
            const token = document.querySelector('input[name="_token"]').value;

            if (!nombre || !monto) {
                alert('Por favor complete los campos obligatorios de la tarifa.');
                return;
            }

            fetch("{{ route('tarifas.store.ajax') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ nombre: nombre, monto_mensual: monto })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('form-tarifa-ajax').reset();
                    cargarTarifasTabla();
                } else {
                    alert('Error al guardar la tarifa.');
                }
            });
        }

        function toggleStatusTarifa(id) {
            const token = document.querySelector('input[name="_token"]').value;
            fetch(`/tarifas/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) { cargarTarifasTabla(); }
            });
        }

        function actualizarSelectPrincipal() {
            fetch("{{ route('tarifas.json') }}", {
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('select-tarifa');
                if (!select) return;
                const valorActual = select.value;
                select.innerHTML = '<option value="">Seleccione una tarifa...</option>';

                data.forEach(tarifa => {
                    if (Boolean(tarifa.estatus)) {
                        const option = document.createElement('option');
                        option.value = tarifa.id;
                        option.text = `${tarifa.nombre} ($${parseFloat(tarifa.monto_mensual).toFixed(2)})`;
                        if (tarifa.id == valorActual) option.selected = true;
                        select.appendChild(option);
                    }
                });
            });
        }
    </script>
</x-app-layout>