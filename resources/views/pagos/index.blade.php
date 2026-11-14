<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Módulo de Cobranza y Pagos') }}
            </h2>
            <a href="{{ route('pagos.matriz') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow">
                Ver Matriz General →
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">

                <!-- Mensajes -->
                @if (session('success'))
                    <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-3 py-1.5 rounded text-xs">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-3 bg-red-100 border border-red-400 text-red-700 px-3 py-1.5 rounded text-xs">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- 1. BUSCADOR COMPACTO -->
                <form method="GET" action="{{ route('pagos.index') }}" class="flex gap-2 mb-4">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                        placeholder="Buscar por Folio (Ej. TOMA-001) o Nombre..." 
                        class="flex-1 rounded-md border-gray-300 shadow-sm text-xs py-1.5" required>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-md text-xs font-semibold shadow">
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('pagos.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded-md text-xs font-semibold flex items-center">
                            Limpiar
                        </a>
                    @endif
                </form>

                @if(isset($usuario))
                                    @php
                    // Cálculos dinámicos basados en los recibos físicos del usuario
                    $pagosRealizados = $usuario->pagos->where('estatus', 'pagado');
                    $pagosPendientes = $usuario->pagos->where('estatus', 'pendiente');

                    $totalPagado = $pagosRealizados->sum('monto');
                    $totalPendiente = $pagosPendientes->sum('monto');
                    $totalGeneral = $totalPagado + $totalPendiente;
                                    @endphp

                                    <!-- 2. CONTENEDOR PRINCIPAL A DOS COLUMNAS -->
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

                                        <!-- ================= COLUMNA IZQUIERDA (4 de 12) ================= -->
                                        <div class="lg:col-span-4 bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm space-y-4">

                                            <!-- Datos del Usuario -->
                                            <div>
                                                <div class="border-b pb-2 mb-3 flex justify-between items-start">
                                                    <div>
                                                        <span class="text-xs font-bold text-blue-600 uppercase">{{ $usuario->folio }}</span>
                                                        <h3 class="text-sm font-bold text-gray-900">{{ $usuario->nombre_completo }}</h3>
                                                    </div>
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-green-100 text-green-800 uppercase">
                                                        {{ $usuario->estado_servicio }}
                                                    </span>
                                                </div>

                                                <div class="space-y-1.5 text-xs text-gray-600">
                                                    <div><span class="font-semibold text-gray-400">Teléfono:</span> {{ $usuario->telefono ?? 'N/D' }}</div>
                                                    <div><span class="font-semibold text-gray-400">Dirección:</span> {{ $usuario->direccion ?? 'N/D' }}</div>
                                                    <div><span class="font-semibold text-gray-400">Red:</span> {{ $usuario->red->nombre ?? 'N/D' }}</div>
                                                    <div><span class="font-semibold text-gray-400">Tarifa Mensual:</span> <span class="font-bold text-blue-600">${{ number_format($usuario->tarifa->monto_mensual ?? 0, 2) }}</span></div>
                                                </div>
                                            </div>

                                            <!-- Resumen de Cuenta con Meses y Adeudos -->
                                            <div class="bg-white p-3 rounded border border-gray-200 space-y-2 text-xs shadow-sm">
                                                <div class="font-semibold text-gray-700 border-b pb-1">Resumen de Cuenta</div>

                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-500">Meses Pagados: <strong class="text-gray-700">({{ $pagosRealizados->count() }})</strong></span> 
                                                    <span class="font-bold text-green-600">${{ number_format($totalPagado, 2) }}</span>
                                                </div>

                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-500">Meses Adeudo: <strong class="text-red-600">({{ $pagosPendientes->count() }})</strong></span> 
                                                    <span class="font-bold text-red-600">${{ number_format($totalPendiente, 2) }}</span>
                                                </div>

                                                <div class="flex justify-between items-center border-t pt-1.5">
                                                    <span class="font-semibold text-gray-700">Total General:</span> 
                                                    <span class="font-bold text-gray-900">${{ number_format($totalGeneral, 2) }}</span>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- ================= COLUMNA DERECHA (8 de 12) ================= -->
                                        <div class="lg:col-span-8 space-y-4">

                                            <!-- Formulario de Registro de Pago -->
                                            <div class="bg-blue-50/60 border border-blue-200 p-3 rounded-lg shadow-sm">
                                                <h4 class="text-xs font-bold text-blue-900 uppercase mb-2">Registrar Nuevo Cobro</h4>
                                                <form action="{{ route('pagos.store') }}" method="POST" class="flex flex-wrap sm:flex-nowrap gap-2 items-end">
                                                    @csrf
                                                    <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">

                                                    <div class="flex-1 min-w-[120px]">
                                                        <label class="block text-[10px] font-medium text-gray-600 mb-0.5">Mes a Cobrar</label>
                                                        <select name="periodo_mes" class="w-full rounded border-gray-300 text-xs py-1" required>
                                                            @foreach(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $mes)
                                                                <option value="{{ $mes }}" {{ date('F') == $mes ? 'selected' : '' }}>{{ $mes }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="w-20">
                                                        <label class="block text-[10px] font-medium text-gray-600 mb-0.5">Año</label>
                                                        <input type="number" name="periodo_anio" value="{{ date('Y') }}" class="w-full rounded border-gray-300 text-xs py-1" required>
                                                    </div>

                                                    <div class="w-24">
                                                        <label class="block text-[10px] font-medium text-gray-600 mb-0.5">Importe ($)</label>
                                                        <input type="number" step="0.01" name="monto" value="{{ $usuario->tarifa->monto_mensual ?? 0 }}" class="w-full rounded border-gray-300 text-xs py-1" required>
                                                    </div>

                                                    <div>
                                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs font-semibold shadow cursor-pointer">
                                                            + Registrar
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Historial de Pagos / Tabla -->
                                            <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                                <div class="bg-gray-100 px-3 py-2 border-b flex justify-between items-center">
                                                    <h4 class="text-xs font-bold text-gray-700 uppercase">Historial de Recibos</h4>
                                                    <span class="text-[10px] font-semibold bg-white px-2 py-0.5 rounded border text-gray-600">{{ count($usuario->pagos) }} registros</span>
                                                </div>
                                                <div class="max-h-64 overflow-y-auto">
                                                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                                                        <thead class="bg-gray-50 sticky top-0">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Periodo</th>
                                                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Importe</th>
                                                                <th class="px-3 py-2 text-center font-medium text-gray-500 uppercase">Estatus</th>
                                                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Fecha de Pago</th>
                                                                <th class="px-3 py-2 text-center font-medium text-gray-500 uppercase">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            @forelse($usuario->pagos as $pago)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $pago->periodo_mes }} {{ $pago->periodo_anio }}</td>
                                                                    <td class="px-3 py-2 text-gray-600">${{ number_format($pago->monto, 2) }}</td>
                                                                    <td class="px-3 py-2 text-center">
                                                                        @if($pago->estatus == 'pagado')
                                                                            <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-green-100 text-green-800">Pagado</span>
                                                                        @else
                                                                            <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-red-100 text-red-800">Pendiente</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-3 py-2 text-gray-500">{{ $pago->fecha_pago ? date('d/m/Y H:i', strtotime($pago->fecha_pago)) : '-' }}</td>
                                                                    <td class="px-3 py-2 text-center">
                                                                        @if($pago->estatus == 'pendiente')
                                                                            <form action="{{ route('pagos.store') }}" method="POST" onsubmit="return confirm('¿Desea registrar el pago de {{ $pago->periodo_mes }} {{ $pago->periodo_anio }} por ${{ number_format($pago->monto, 2) }}?');" class="inline">
                                                                                @csrf
                                                                                <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
                                                                                <input type="hidden" name="periodo_mes" value="{{ $pago->periodo_mes }}">
                                                                                <input type="hidden" name="periodo_anio" value="{{ $pago->periodo_anio }}">
                                                                                <input type="hidden" name="monto" value="{{ $pago->monto }}">
                                                                                <button type="submit" style="background-color: #16a34a; color: #ffffff;" class="px-2.5 py-1 rounded text-[11px] font-semibold shadow-sm cursor-pointer hover:bg-green-700">
                                                                                    Pagar
                                                                                </button>
                                                                            </form>
                                                                        @else
                                                                            <a href="{{ route('pagos.ticket', $pago->id) }}" target="_blank"
                                                                                class="bg-gray-800 hover:bg-gray-900 text-white px-2 py-1 rounded text-[11px] font-semibold shadow-sm transition"
                                                                                title="Imprimir Ticket">
                                                                                🖨️ Ticket
                                                                            </a>

                                                                            <!-- Botón Anular -->
                                                                            <form action="{{ route('pagos.anular', $pago->id) }}" method="POST"
                                                                                onsubmit="return confirm('¿Está seguro de anular este pago?');" class="inline">
                                                                                @csrf
                                                                                <button type="submit"
                                                                                    class="bg-red-600 hover:bg-red-700 text-white px-2.5 py-1 rounded text-[11px] font-semibold shadow-sm transition cursor-pointer">
                                                                                    Anular
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="5" class="px-3 py-4 text-center text-gray-400 text-xs">Sin pagos registrados para este usuario.</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                @else
                    <div class="text-center py-12 text-gray-400 text-xs">
                        Ingresa un folio o nombre arriba para consultar y cobrar.
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>