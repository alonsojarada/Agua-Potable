<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Padrón de Tomas de Agua (Usuarios)') }}
            </h2>
            <a href="{{ route('usuarios.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-semibold shadow">
                + Nueva Toma
            </a>
        </div>
    </x-slot>

    <!-- CSS de Leaflet para el mapa del modal -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Folio</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Titular / Dirección</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Red Asignada</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tarifa Mensual</th> <!-- <-- NUEVA COLUMNA -->
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($usuarios as $usuario)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        {{ $usuario->folio }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="font-medium text-gray-900">{{ $usuario->nombre_completo }}</div>
                                        <div class="text-xs text-gray-500">{{ $usuario->direccion }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $usuario->red->nombre ?? 'Sin red' }}
                                    </td>
                                    <!-- NUEVA CELDA PARA MOSTRAR LA TARIFA -->
                                    <!-- Celda para mostrar únicamente el monto de la tarifa -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">
                                        @if($usuario->tarifa)
                                            ${{ number_format($usuario->tarifa->monto_mensual, 2) }}
                                        @else
                                            <span class="text-xs text-gray-400 italic font-normal">Sin tarifa</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if($usuario->estado_servicio === 'activo')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                        @elseif($usuario->estado_servicio === 'suspendido')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Suspendido</span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cortado</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button" onclick="abrirModalToma(
                                                    '{{ $usuario->folio }}', 
                                                    '{{ addslashes($usuario->nombre_completo) }}', 
                                                    '{{ $usuario->latitud }}', 
                                                    '{{ $usuario->longitud }}'
                                                )"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3 font-semibold cursor-pointer">
                                            Ver
                                        </button>

                                        <a href="{{ route('usuarios.edit', $usuario) }}"
                                            class="text-yellow-600 hover:text-yellow-900 mr-3">Editar</a>
                                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('¿Estás seguro de eliminar esta toma?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No hay tomas registradas en el padrón todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $usuarios->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL VISTA RÁPIDA (SOLO MAPA Y MARCADOR) -->
    <div id="modal-toma" onclick="if(event.target === this) cerrarModalToma()"
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-60 flex items-center justify-center p-4"
        style="display: none;">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-xl sm:w-full p-3">
            <div class="flex justify-between items-center pb-2 mb-2 border-b">
                <h3 class="text-sm font-bold text-gray-800 truncate pr-2">
                    <span id="modal-folio" class="text-blue-600"></span> -
                    <span id="modal-nombre"></span>
                </h3>
                <button type="button" onclick="cerrarModalToma()"
                    class="text-gray-400 hover:text-gray-700 font-bold text-xl leading-none cursor-pointer">&times;</button>
            </div>

            <!-- Contenedor Único del Mapa -->
            <div id="mapa-detalle" style="height: 360px;" class="w-full rounded-md border shadow-inner"></div>
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