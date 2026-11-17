<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center min-w-0">
            <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight truncate min-w-0"
                title="Mapa General: Redes, Tomas y Horarios">
                {{ __('Mapa General: Redes, Tomas y Horarios') }}
            </h2>
        </div>
    </x-slot>

    <!-- CSS de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- CONTENEDOR DE ANCHO 100% DINÁMICO -->
    <div class="py-2 px-2 sm:px-3 w-full">
        <div class="flex flex-col lg:flex-row gap-2.5 items-stretch">

            <!-- PANEL IZQUIERDO COMPACTO (w-80 fijo con escala de texto adaptativa) -->
            <div
                class="w-full lg:w-80 bg-white overflow-hidden shadow-2xs rounded-lg p-2.5 flex flex-col h-[520px] lg:h-[calc(100vh-120px)] shrink-0">
                <h3 class="font-bold text-gray-800 text-xs sm:text-sm mb-0.5">Panel de Control</h3>
                <p class="text-[10px] sm:text-[11px] text-gray-500 mb-1.5">Filtra circuitos, tomas e inspecciona horarios.</p>

                <!-- Controles Globales Rápidos -->
                <div class="mb-1.5 p-1.5 bg-slate-50 rounded-md border border-gray-200 text-xs space-y-1.5 shrink-0">
                    <div class="grid grid-cols-2 gap-1.5">
                        <div>
                            <span class="font-bold text-gray-700 block text-[10px] sm:text-[11px]">Tuberías (Redes):</span>
                            <div class="space-x-1">
                                <button type="button" id="btn-redes-todas"
                                    class="text-blue-600 hover:underline font-semibold text-[10px] sm:text-[11px] cursor-pointer">Todas</button>
                                <span class="text-gray-300">|</span>
                                <button type="button" id="btn-redes-ninguna"
                                    class="text-red-600 hover:underline font-semibold text-[10px] sm:text-[11px] cursor-pointer">Ninguna</button>
                            </div>
                        </div>
                        <div class="border-l border-gray-200 pl-1.5">
                            <span class="font-bold text-gray-700 block text-[10px] sm:text-[11px]">Tomas (Usuarios):</span>
                            <div class="space-x-1">
                                <button type="button" id="btn-tomas-todas"
                                    class="text-emerald-600 hover:underline font-semibold text-[10px] sm:text-[11px] cursor-pointer">Todas</button>
                                <span class="text-gray-300">|</span>
                                <button type="button" id="btn-tomas-ninguna"
                                    class="text-red-600 hover:underline font-semibold text-[10px] sm:text-[11px] cursor-pointer">Ninguna</button>
                            </div>
                        </div>
                    </div>

                    <!-- Checkbox Global para Mostrar/Ocultar Horarios -->
                    <div class="pt-1 border-t border-gray-200">
                        <label
                            class="flex items-center space-x-1.5 cursor-pointer text-gray-700 hover:text-gray-900 text-[10px] sm:text-[11px]">
                            <input type="checkbox" id="cb-mostrar-horarios"
                                class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-3.5 h-3.5">
                            <span class="font-bold">Mostrar horarios de suministro</span>
                        </label>
                    </div>
                </div>

                <!-- LISTADO DE REDES Y USUARIOS -->
                <div
                    class="overflow-y-auto flex-1 space-y-1.5 pr-1 border border-gray-100 rounded-md p-1 bg-slate-50/40">
                    @foreach($redes as $red)
                        <div class="bg-white rounded-md border border-gray-200 shadow-2xs overflow-hidden text-xs">
                            <!-- Cabecera de la Red -->
                            <div class="p-2 bg-white hover:bg-slate-50 transition">
                                <div class="flex items-center justify-between mb-0.5">
                                    <label class="flex items-center space-x-1.5 cursor-pointer overflow-hidden">
                                        <input type="checkbox" value="{{ $red->id }}"
                                            class="cb-red-tuberia rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5">
                                        <span class="text-[11px] sm:text-xs font-bold text-gray-800 truncate"
                                            title="{{ $red->nombre }}">{{ $red->nombre }}</span>
                                    </label>
                                    <span
                                        class="text-[9px] sm:text-[10px] bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded-full font-semibold shrink-0">
                                        {{ $red->tramos->count() }} tramos
                                    </span>
                                </div>

                                <!-- Bloque de Horarios -->
                                <div class="seccion-horario-red hidden">
                                    <div
                                        class="my-1 px-1.5 py-0.5 bg-amber-50/70 border border-amber-200 rounded flex items-center justify-between text-[10px] sm:text-[11px]">
                                        <span class="font-semibold text-amber-800">Suministro
                                            ({{ $red->horarios->count() }})</span>
                                        @if($red->horarios->count() > 0)
                                            <button type="button"
                                                onclick="toggleAcordeon('lista-horarios-{{ $red->id }}', this)"
                                                class="text-amber-700 hover:text-amber-900 font-semibold flex items-center space-x-1 cursor-pointer">
                                                <span>Ver horario</span>
                                                <span class="arrow-icon text-[9px]">&#9660;</span>
                                            </button>
                                        @else
                                            <span class="text-gray-400 italic text-[10px]">Sin horario</span>
                                        @endif
                                    </div>

                                    @if($red->horarios->count() > 0)
                                        <div id="lista-horarios-{{ $red->id }}"
                                            class="hidden mb-1 px-2 py-1 bg-amber-50 border border-amber-200 rounded text-[10px] text-amber-800 space-y-0.5">
                                            <ul class="list-disc list-inside">
                                                @foreach($red->horarios as $h)
                                                    <li><strong>{{ $h->dia_semana }}:</strong>
                                                        {{ date('H:i', strtotime($h->hora_inicio)) }} -
                                                        {{ date('H:i', strtotime($h->hora_fin)) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <!-- Barra de control de Tomas -->
                                <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-[10px] sm:text-[11px]">
                                    <label
                                        class="flex items-center space-x-1.5 cursor-pointer text-gray-600 hover:text-gray-900">
                                        <input type="checkbox" data-red-id="{{ $red->id }}"
                                            class="cb-red-tomas rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                                        <span class="font-medium">Tomas ({{ $red->usuarios->count() }})</span>
                                    </label>

                                    @if($red->usuarios->count() > 0)
                                        <button type="button" onclick="toggleAcordeon('lista-tomas-{{ $red->id }}', this)"
                                            class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center space-x-0.5 cursor-pointer text-[10px] sm:text-[11px]">
                                            <span>Ver lista</span>
                                            <span class="arrow-icon text-[9px]">&#9660;</span>
                                        </button>
                                    @else
                                        <span class="text-gray-400 italic text-[10px]">Sin tomas</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Lista Desplegable de Tomas Individuales -->
                            @if($red->usuarios->count() > 0)
                                <div id="lista-tomas-{{ $red->id }}"
                                    class="hidden bg-slate-50 border-t border-gray-200 p-1.5 space-y-1 max-h-40 overflow-y-auto">
                                    @foreach($red->usuarios as $usuario)
                                        @if($usuario->latitud && $usuario->longitud)
                                            <label
                                                class="flex items-center justify-between p-1 rounded hover:bg-white cursor-pointer text-[10px] sm:text-[11px] border border-transparent hover:border-gray-200">
                                                <div class="flex items-center space-x-1.5 overflow-hidden">
                                                    <input type="checkbox" value="{{ $usuario->id }}" data-red-id="{{ $red->id }}"
                                                        class="cb-toma-individual cb-toma-red-{{ $red->id }} rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                                                    <span class="text-gray-700 truncate" title="{{ $usuario->nombre_completo }}">
                                                        <strong class="text-gray-900">{{ $usuario->folio }}</strong> -
                                                        {{ $usuario->nombre_completo }}
                                                    </span>
                                                </div>
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full shrink-0 ml-1
                                                                    {{ $usuario->estado_servicio === 'activo' ? 'bg-green-500' : ($usuario->estado_servicio === 'suspendido' ? 'bg-amber-500' : 'bg-red-500') }}"
                                                    title="{{ ucfirst($usuario->estado_servicio) }}">
                                                </span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- PANEL DERECHO: Mapa y Simulación -->
            <div
                class="flex-1 bg-white overflow-hidden shadow-2xs rounded-lg p-2.5 flex flex-col h-[520px] lg:h-[calc(100vh-120px)] min-w-0">

                <!-- BARRA DE SIMULACIÓN COMPACTA -->
                <div class="mb-2 p-1.5 bg-slate-50 border border-slate-200 rounded-md shadow-2xs shrink-0">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">

                        <div class="md:col-span-3 flex flex-col">
                            <div class="max-h-12 overflow-y-auto pr-1">
                                <div id="lista-texto-horarios-dia" class="text-[10px] text-slate-600 leading-tight">
                                </div>
                            </div>
                        </div>

                        <div
                            class="md:col-span-9 flex flex-col space-y-1 pl-0 md:pl-2 border-t md:border-t-0 md:border-l border-slate-200 pt-1 md:pt-0">
                            <div class="flex items-center justify-between text-[11px]">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">DÍA:</span>
                                    <select id="sim-dia"
                                        class="rounded border-gray-300 text-[10px] sm:text-xs shadow-2xs focus:border-blue-500 focus:ring-blue-500 py-0.5 px-1.5 w-24">
                                        <option value="Lunes">Lunes</option>
                                        <option value="Martes">Martes</option>
                                        <option value="Miércoles">Miércoles</option>
                                        <option value="Jueves">Jueves</option>
                                        <option value="Viernes">Viernes</option>
                                        <option value="Sábado">Sábado</option>
                                        <option value="Domingo">Domingo</option>
                                    </select>
                                    <div class="truncate px-1 text-[10px] sm:text-xs">
                                        <span class="text-slate-500">Red:</span>
                                        <span id="sim-red-activa-label"
                                            class="text-emerald-700 font-bold ml-0.5">Ninguna</span>
                                    </div>
                                </div>
                                <div class="shrink-0 text-[10px] sm:text-xs">
                                    <span class="text-slate-500">Hora:</span>
                                    <span id="sim-hora-label" class="text-blue-600 font-bold ml-0.5">00:00 hrs</span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <button type="button" id="sim-play-btn"
                                    class="bg-blue-600 hover:bg-blue-700 text-white w-5 h-5 rounded-full flex items-center justify-center shadow-2xs transition shrink-0 cursor-pointer"
                                    title="Reproducir / Pausa">
                                    <span id="sim-play-icon" class="text-[8px] font-bold">&#9658;</span>
                                </button>

                                <div class="flex-1">
                                    <input type="range" id="sim-slider" min="0" max="23.75" value="0" step="0.25"
                                        style="background: #cbd5e1;"
                                        class="w-full h-1 rounded-lg appearance-none cursor-pointer accent-blue-700 border border-slate-300">

                                    <div
                                        class="flex justify-between text-[9px] text-slate-500 font-mono mt-0.5 select-none px-0.5">
                                        <span>00</span><span>03</span><span>06</span><span>09</span>
                                        <span>12</span><span>15</span><span>18</span><span>21</span><span>23</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Contenedor del Mapa -->
                <div id="map-general" class="w-full h-[450px] lg:h-auto lg:flex-1 rounded-md shadow-inner z-0"></div>
            </div>

        </div>
    </div>

    <!-- JS de Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        function toggleAcordeon(elementId, btn) {
            const el = document.getElementById(elementId);
            const arrow = btn.querySelector('.arrow-icon');
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
                arrow.innerHTML = '&#9650;';
            } else {
                el.classList.add('hidden');
                arrow.innerHTML = '&#9660;';
            }
        }

        function normalizarTexto(texto) {
            if (!texto) return '';
            return texto.toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "").trim();
        }

        function extraerHoraDecimal(timeStr) {
            if (!timeStr) return 0;
            if (typeof timeStr === 'string' && timeStr.includes(' ')) {
                timeStr = timeStr.split(' ')[1];
            }
            const partes = String(timeStr).split(':');
            const horas = parseInt(partes[0], 10) || 0;
            const minutos = parseInt(partes[1], 10) || 0;
            return horas + (minutos / 60);
        }

        document.addEventListener("DOMContentLoaded", function () {
            var redes = @json($redes);
            var usuarios = @json($usuarios);

            var defaultLat = 25.3263827;
            var defaultLng = -103.295072;

            if (usuarios.length > 0 && usuarios[0].latitud && usuarios[0].longitud) {
                defaultLat = parseFloat(usuarios[0].latitud);
                defaultLng = parseFloat(usuarios[0].longitud);
            }

            // 1. Definir las capas base (Esri Satelital y Google Maps)
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

            // Inicializar el mapa con Esri por defecto
            var map = L.map('map-general', {
                layers: [capaEsri]
            }).setView([defaultLat, defaultLng], 15);

            // 2. Crear control personalizado de botones flotantes en la esquina superior derecha
            var SelectorCapas = L.Control.extend({
                options: { position: 'topright' },

                onAdd: function (map) {
                    var contenedor = L.DomUtil.create('div', 'leaflet-bar leaflet-control bg-white shadow-md rounded-md p-1 flex gap-1 text-xs font-semibold z-[999]');
                    
                    contenedor.innerHTML = `
                        <button type="button" id="btn-esri" class="px-2.5 py-1 rounded bg-blue-600 text-white cursor-pointer transition">Esri / Sat</button>
                        <button type="button" id="btn-google-sat" class="px-2.5 py-1 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 cursor-pointer transition">Google Sat</button>
                        <button type="button" id="btn-google-road" class="px-2.5 py-1 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 cursor-pointer transition">Google Calles</button>
                    `;

                    L.DomEvent.disableClickPropagation(contenedor);
                    return contenedor;
                }
            });

            map.addControl(new SelectorCapas());

            // 3. Eventos para alternar entre los mapas mediante los botones
            document.getElementById('btn-esri').addEventListener('click', function () {
                map.eachLayer(layer => { if (layer instanceof L.TileLayer) map.removeLayer(layer); });
                capaEsri.addTo(map);
                actualizarEstilosBotones(this);
            });

            document.getElementById('btn-google-sat').addEventListener('click', function () {
                map.eachLayer(layer => { if (layer instanceof L.TileLayer) map.removeLayer(layer); });
                capaGoogleSat.addTo(map);
                actualizarEstilosBotones(this);
            });

            document.getElementById('btn-google-road').addEventListener('click', function () {
                map.eachLayer(layer => { if (layer instanceof L.TileLayer) map.removeLayer(layer); });
                capaGoogleRoad.addTo(map);
                actualizarEstilosBotones(this);
            });

            function actualizarEstilosBotones(btnActivo) {
                ['btn-esri', 'btn-google-sat', 'btn-google-road'].forEach(id => {
                    let b = document.getElementById(id);
                    if (b) b.className = "px-2.5 py-1 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 cursor-pointer transition";
                });
                if (btnActivo) btnActivo.className = "px-2.5 py-1 rounded bg-blue-600 text-white cursor-pointer transition shadow-xs";
            }

            setTimeout(() => { map.invalidateSize(); }, 250);

            var redLayers = {};
            var markerLayers = {};
            var allBounds = L.featureGroup();

            redes.forEach(function (red) {
                var redGroup = L.layerGroup();

                let horariosHtml = '<ul style="margin: 0; padding-left: 15px; font-size: 11px;">';
                if (red.horarios && red.horarios.length > 0) {
                    red.horarios.forEach(h => {
                        let hInicio = h.hora_inicio ? h.hora_inicio.substring(0, 5) : '';
                        let hFin = h.hora_fin ? h.hora_fin.substring(0, 5) : '';
                        horariosHtml += `<li><b>${h.dia_semana}:</b> ${hInicio} - ${hFin} hrs</li>`;
                    });
                } else {
                    horariosHtml += '<li style="color: gray;">Sin horarios programados</li>';
                }
                horariosHtml += '</ul>';

                if (red.tramos && red.tramos.length > 0) {
                    red.tramos.forEach(function (tramo) {
                        if (tramo.coordenadas_linea && Array.isArray(tramo.coordenadas_linea)) {
                            var latLngs = tramo.coordenadas_linea.map(coord => [coord.lat, coord.lng]);

                            if (latLngs.length > 0) {
                                var polyline = L.polyline(latLngs, {
                                    color: '#06b6d4',
                                    weight: 5,
                                    opacity: 0.9
                                });

                                polyline.bindPopup(`
                                    <div class="p-1 text-xs">
                                        <strong>Circuito:</strong> ${red.nombre}<br>
                                        <strong>Tramo:</strong> ${tramo.nombre_tramo} (${tramo.diametro_pulgadas}")<br>
                                        <hr class="my-1">
                                        <strong>Horarios:</strong>
                                        ${horariosHtml}
                                    </div>
                                `);

                                redGroup.addLayer(polyline);
                                allBounds.addLayer(polyline);
                            }
                        }
                    });
                }

                redLayers[red.id] = redGroup;
            });

            usuarios.forEach(function (usuario) {
                if (usuario.latitud && usuario.longitud) {
                    let estadoBadge = '<span style="color: green; font-weight: bold;">ACTIVO</span>';
                    if (usuario.estado_servicio === 'suspendido') estadoBadge = '<span style="color: orange; font-weight: bold;">SUSPENDIDO</span>';
                    if (usuario.estado_servicio === 'cortado') estadoBadge = '<span style="color: red; font-weight: bold;">CORTADO</span>';

                    var popupContent = `
                        <div class="p-1 text-xs">
                            <strong>Folio:</strong> ${usuario.folio}<br>
                            <strong>Titular:</strong> ${usuario.nombre_completo}<br>
                            <strong>Dirección:</strong> ${usuario.direccion}<br>
                            <strong>Red:</strong> ${usuario.red ? usuario.red.nombre : 'N/A'}<br>
                            <strong>Estado:</strong> ${estadoBadge}
                        </div>
                    `;

                    var marker = L.marker([usuario.latitud, usuario.longitud]).bindPopup(popupContent);
                    markerLayers[usuario.id] = marker;
                    allBounds.addLayer(marker);
                }
            });

            if (allBounds.getLayers().length > 0) {
                map.fitBounds(allBounds.getBounds(), { padding: [30, 30] });
            }

            function formatearHoraLabel(valorDecimal) {
                let horas = Math.floor(valorDecimal);
                let minutos = Math.round((valorDecimal - horas) * 60);
                let hStr = horas < 10 ? '0' + horas : horas;
                let mStr = minutos < 10 ? '0' + minutos : minutos;
                return hStr + ':' + mStr + ' hrs';
            }

            let simInterval = null;
            const sliderHora = document.getElementById('sim-slider');
            const horaLabel = document.getElementById('sim-hora-label');
            const selectDia = document.getElementById('sim-dia');
            const playBtn = document.getElementById('sim-play-btn');
            const playIcon = document.getElementById('sim-play-icon');
            const listaTextoHorariosDia = document.getElementById('lista-texto-horarios-dia');
            const simRedActivaLabel = document.getElementById('sim-red-activa-label');

            function verificarCoincidenciaDia(diaHorarioStr, diaSeleccionadoStr) {
                let diaHorarioNorm = normalizarTexto(diaHorarioStr);
                let diaSelNorm = normalizarTexto(diaSeleccionadoStr);

                return (diaHorarioNorm === diaSelNorm) ||
                    (diaHorarioNorm === 'lunes a viernes' && ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'].includes(diaSelNorm)) ||
                    (diaHorarioNorm === 'fines de semana' && ['sabado', 'domingo'].includes(diaSelNorm));
            }

            function actualizarListaTextoHorarios() {
                const diaSel = selectDia.value;
                let turnosArray = [];

                if (redes && redes.length > 0) {
                    redes.forEach(red => {
                        if (red.horarios && Array.isArray(red.horarios)) {
                            red.horarios.forEach(h => {
                                if (verificarCoincidenciaDia(h.dia_semana, diaSel)) {
                                    let hIniStr = h.hora_inicio ? h.hora_inicio.substring(0, 5) : '00:00';
                                    let hFinStr = h.hora_fin ? h.hora_fin.substring(0, 5) : '00:00';
                                    let decimalInicio = extraerHoraDecimal(h.hora_inicio);

                                    turnosArray.push({
                                        nombreRed: red.nombre,
                                        inicioStr: hIniStr,
                                        finStr: hFinStr,
                                        decimalInicio: decimalInicio
                                    });
                                }
                            });
                        }
                    });
                }

                turnosArray.sort((a, b) => a.decimalInicio - b.decimalInicio);
                let turnosTexto = turnosArray.map(t => `• <b>${t.nombreRed}</b>: ${t.inicioStr} - ${t.finStr}`);

                if (turnosTexto.length === 0) {
                    listaTextoHorariosDia.innerHTML = '<span class="text-gray-400 italic">Sin turnos.</span>';
                } else {
                    listaTextoHorariosDia.innerHTML = turnosTexto.join('<br>');
                }
            }

            function hayServicioEnHora(diaNorm, horaDecimal) {
                if (!redes || redes.length === 0) return false;

                for (let i = 0; i < redes.length; i++) {
                    const red = redes[i];
                    if (red.horarios && Array.isArray(red.horarios)) {
                        for (let j = 0; j < red.horarios.length; j++) {
                            const h = red.horarios[j];
                            if (verificarCoincidenciaDia(h.dia_semana, diaNorm) && h.hora_inicio && h.hora_fin) {
                                const hInicio = extraerHoraDecimal(h.hora_inicio);
                                const hFin = extraerHoraDecimal(h.hora_fin);

                                if (horaDecimal >= hInicio && horaDecimal < hFin) return true;
                            }
                        }
                    }
                }
                return false;
            }

            function actualizarBarraVisualServicio() {
                const diaSeleccionado = normalizarTexto(selectDia.value);
                const totalPasos = 96;
                let stops = [];

                for (let paso = 0; paso < totalPasos; paso++) {
                    const horaActual = paso * 0.25;
                    const pctInicio = (paso / totalPasos) * 100;
                    const pctFin = ((paso + 1) / totalPasos) * 100;

                    const color = hayServicioEnHora(diaSeleccionado, horaActual) ? '#3b82f6' : '#cbd5e1';
                    stops.push(`${color} ${pctInicio.toFixed(2)}%`);
                    stops.push(`${color} ${pctFin.toFixed(2)}%`);
                }

                sliderHora.style.background = `linear-gradient(to right, ${stops.join(', ')})`;
            }

            function ejecutarSimulacion() {
                const diaSeleccionado = normalizarTexto(selectDia.value);
                const horaActualDecimal = parseFloat(sliderHora.value);
                horaLabel.innerText = formatearHoraLabel(horaActualDecimal);

                let redesActivasIds = [];
                if (!redes || redes.length === 0) return;

                redes.forEach(red => {
                    let tieneServicioAhora = false;
                    if (red.horarios && Array.isArray(red.horarios) && red.horarios.length > 0) {
                        red.horarios.forEach(h => {
                            if (verificarCoincidenciaDia(h.dia_semana, diaSeleccionado) && h.hora_inicio && h.hora_fin) {
                                let hInicioDec = extraerHoraDecimal(h.hora_inicio);
                                let hFinDec = extraerHoraDecimal(h.hora_fin);
                                if (horaActualDecimal >= hInicioDec && horaActualDecimal < hFinDec) tieneServicioAhora = true;
                            }
                        });
                    }
                    if (tieneServicioAhora) redesActivasIds.push(red.id);
                });

                if (simRedActivaLabel) {
                    if (redesActivasIds.length === 0) {
                        simRedActivaLabel.innerText = 'Ninguna';
                        simRedActivaLabel.className = 'text-slate-400 font-bold ml-0.5';
                    } else {
                        let nombres = redesActivasIds.map(id => {
                            let r = redes.find(net => net.id === id);
                            return r ? r.nombre : '';
                        }).filter(Boolean);
                        simRedActivaLabel.innerText = nombres.join(' y ');
                        simRedActivaLabel.className = 'text-emerald-700 font-bold ml-0.5';
                    }
                }

                redes.forEach(red => {
                    const redId = red.id;
                    const layerGroup = redLayers[redId];
                    const checkboxRed = document.querySelector(`.cb-red-tuberia[value="${redId}"]`);
                    const estaActiva = redesActivasIds.includes(redId);

                    if (layerGroup) {
                        if (estaActiva) {
                            if (!map.hasLayer(layerGroup)) map.addLayer(layerGroup);
                            if (checkboxRed) checkboxRed.checked = true;
                        } else {
                            if (map.hasLayer(layerGroup)) map.removeLayer(layerGroup);
                            if (checkboxRed) checkboxRed.checked = false;
                        }
                    }
                });

                document.querySelectorAll('.cb-toma-individual').forEach(cb => cb.checked = false);
                document.querySelectorAll('.cb-red-tomas').forEach(cbPadre => {
                    cbPadre.checked = false;
                    cbPadre.indeterminate = false;
                });

                usuarios.forEach(usuario => {
                    const usuarioId = usuario.id;
                    const marker = markerLayers[usuarioId];
                    const redId = usuario.red_id;
                    const redActiva = redesActivasIds.includes(redId);
                    const checkboxToma = document.querySelector(`input.cb-toma-individual[value="${usuarioId}"]`);

                    if (marker) {
                        if (redActiva) {
                            if (!map.hasLayer(marker)) map.addLayer(marker);
                            if (checkboxToma) checkboxToma.checked = true;
                        } else {
                            if (map.hasLayer(marker)) map.removeLayer(marker);
                            if (checkboxToma) checkboxToma.checked = false;
                        }
                    }
                });

                document.querySelectorAll('.cb-red-tomas').forEach(cbPadre => {
                    const redId = cbPadre.dataset.redId;
                    const todosHijos = document.querySelectorAll(`.cb-toma-red-${redId}`);
                    const hijosMarcados = document.querySelectorAll(`.cb-toma-red-${redId}:checked`);

                    if (todosHijos.length > 0) {
                        cbPadre.checked = (hijosMarcados.length === todosHijos.length);
                        cbPadre.indeterminate = (hijosMarcados.length > 0 && hijosMarcados.length < todosHijos.length);
                    }
                });
            }

            sliderHora.addEventListener('input', ejecutarSimulacion);

            selectDia.addEventListener('change', function () {
                if (simInterval) {
                    clearInterval(simInterval);
                    simInterval = null;
                    playIcon.innerHTML = '&#9658;';
                    playBtn.classList.remove('bg-amber-600', 'hover:bg-amber-700');
                    playBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }
                sliderHora.value = 0;
                actualizarListaTextoHorarios();
                actualizarBarraVisualServicio();
                ejecutarSimulacion();
            });

            playBtn.addEventListener('click', function () {
                if (simInterval) {
                    clearInterval(simInterval);
                    simInterval = null;
                    playIcon.innerHTML = '&#9658;';
                    playBtn.classList.remove('bg-amber-600', 'hover:bg-amber-700');
                    playBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                } else {
                    let horaVal = parseFloat(sliderHora.value);
                    if (horaVal >= 23.75) {
                        sliderHora.value = 0;
                        ejecutarSimulacion();
                    }

                    playIcon.innerHTML = '&#10074;&#10074;';
                    playBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    playBtn.classList.add('bg-amber-600', 'hover:bg-amber-700');

                    simInterval = setInterval(() => {
                        horaVal = parseFloat(sliderHora.value);
                        if (horaVal < 23.75) {
                            horaVal += 0.25;
                            sliderHora.value = horaVal;
                            ejecutarSimulacion();
                        } else {
                            sliderHora.value = 0;
                            ejecutarSimulacion();
                            clearInterval(simInterval);
                            simInterval = null;
                            playIcon.innerHTML = '&#9658;';
                            playBtn.classList.remove('bg-amber-600', 'hover:bg-amber-700');
                            playBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                        }
                    }, 600);
                }
            });

            const cbMostrarHorarios = document.getElementById('cb-mostrar-horarios');
            if (cbMostrarHorarios) {
                cbMostrarHorarios.addEventListener('change', function () {
                    document.querySelectorAll('.seccion-horario-red').forEach(seccion => {
                        if (this.checked) seccion.classList.remove('hidden');
                        else seccion.classList.add('hidden');
                    });
                });
            }

            document.querySelectorAll('.cb-red-tuberia').forEach(cb => {
                cb.addEventListener('change', function () {
                    const redId = this.value;
                    if (redLayers[redId]) {
                        this.checked ? map.addLayer(redLayers[redId]) : map.removeLayer(redLayers[redId]);
                    }
                });
            });

            document.querySelectorAll('.cb-red-tomas').forEach(cbPadre => {
                cbPadre.addEventListener('change', function () {
                    const redId = cbPadre.dataset.redId;
                    const isChecked = this.checked;
                    this.indeterminate = false;

                    document.querySelectorAll(`.cb-toma-red-${redId}`).forEach(cbHijo => {
                        cbHijo.checked = isChecked;
                        const marker = markerLayers[cbHijo.value];
                        if (marker) isChecked ? map.addLayer(marker) : map.removeLayer(marker);
                    });
                });
            });

            document.querySelectorAll('.cb-toma-individual').forEach(cbHijo => {
                cbHijo.addEventListener('change', function () {
                    const usuarioId = this.value;
                    const redId = this.dataset.redId;
                    const marker = markerLayers[usuarioId];

                    if (marker) this.checked ? map.addLayer(marker) : map.removeLayer(marker);

                    const todosHijos = document.querySelectorAll(`.cb-toma-red-${redId}`);
                    const hijosMarcados = document.querySelectorAll(`.cb-toma-red-${redId}:checked`);
                    const cbPadre = document.querySelector(`.cb-red-tomas[data-red-id="${redId}"]`);

                    if (cbPadre) {
                        if (hijosMarcados.length === 0) {
                            cbPadre.checked = false;
                            cbPadre.indeterminate = false;
                        } else if (hijosMarcados.length === todosHijos.length) {
                            cbPadre.checked = true;
                            cbPadre.indeterminate = false;
                        } else {
                            cbPadre.checked = false;
                            cbPadre.indeterminate = true;
                        }
                    }
                });
            });

            document.getElementById('btn-redes-todas').addEventListener('click', () => {
                document.querySelectorAll('.cb-red-tuberia').forEach(cb => {
                    if (!cb.checked) { cb.checked = true; cb.dispatchEvent(new Event('change')); }
                });
            });
            document.getElementById('btn-redes-ninguna').addEventListener('click', () => {
                document.querySelectorAll('.cb-red-tuberia').forEach(cb => {
                    if (cb.checked) { cb.checked = false; cb.dispatchEvent(new Event('change')); }
                });
            });

            document.getElementById('btn-tomas-todas').addEventListener('click', () => {
                document.querySelectorAll('.cb-red-tomas').forEach(cb => {
                    cb.checked = true;
                    cb.dispatchEvent(new Event('change'));
                });
            });
            document.getElementById('btn-tomas-ninguna').addEventListener('click', () => {
                document.querySelectorAll('.cb-red-tomas').forEach(cb => {
                    cb.checked = false;
                    cb.dispatchEvent(new Event('change'));
                });
            });

            actualizarListaTextoHorarios();
            actualizarBarraVisualServicio();
            ejecutarSimulacion();
        });
    </script>
</x-app-layout>