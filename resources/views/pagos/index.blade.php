<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center min-w-0">
            <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight truncate min-w-0" title="Módulo de Cobranza y Pagos">
                {{ __('Módulo de Cobranza y Pagos') }}
            </h2>
        </div>
    </x-slot>

    <style>
        .cobranza-ventana-fluida {
            font-size: clamp(0.65rem, 1.25vw, 0.875rem);
        }
        .cobranza-ventana-fluida .titulo-fluido {
            font-size: clamp(0.72rem, 1.4vw, 1rem);
        }
        .cobranza-ventana-fluida .subtexto-fluido {
            font-size: clamp(0.6rem, 1.1vw, 0.78rem);
        }
        .cobranza-ventana-fluida .control-fluido {
            font-size: clamp(0.65rem, 1.2vw, 0.875rem);
            padding: clamp(0.22rem, 0.55vw, 0.45rem) clamp(0.35rem, 0.8vw, 0.65rem);
        }
        .cobranza-ventana-fluida .btn-fluido {
            font-size: clamp(0.62rem, 1.15vw, 0.85rem);
            padding: clamp(0.22rem, 0.55vw, 0.42rem) clamp(0.4rem, 0.9vw, 0.85rem);
        }
        .cobranza-ventana-fluida .badge-fluido {
            font-size: clamp(0.56rem, 1vw, 0.75rem);
            padding: clamp(0.1rem, 0.3vw, 0.18rem) clamp(0.3rem, 0.6vw, 0.5rem);
        }
        .cobranza-ventana-fluida table th,
        .cobranza-ventana-fluida table td {
            font-size: clamp(0.6rem, 1.2vw, 0.875rem);
            padding: clamp(0.25rem, 0.65vw, 0.5rem) clamp(0.3rem, 0.85vw, 0.75rem);
        }
    </style>

    <!-- CONTENEDOR DE ANCHO COMPLETO Y DINÁMICO -->
    <div class="py-1.5 sm:py-2 px-1.5 sm:px-3 w-full cobranza-ventana-fluida">
        <div class="bg-white overflow-hidden shadow-2xs rounded-lg p-2 sm:p-3 w-full">

            <!-- Mensajes -->
            @if (session('success'))
                <div class="mb-2 sm:mb-3 bg-green-100 border border-green-400 text-green-700 px-2.5 sm:px-3 py-1.5 rounded subtexto-fluido">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-2 sm:mb-3 bg-red-100 border border-red-400 text-red-700 px-2.5 sm:px-3 py-1.5 rounded subtexto-fluido">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. BARRA SUPERIOR: BUSCADOR Y BOTÓN MATRIZ EN UNA SOLA FILA CON SCROLL SI ES MUY CHICO -->
            <div class="overflow-x-auto mb-2.5 sm:mb-3 bg-slate-50 p-1.5 sm:p-2.5 rounded-md border border-gray-200">
                <div class="flex flex-row items-center justify-between gap-1.5 sm:gap-2 min-w-max sm:min-w-0">
                    <form method="GET" action="{{ route('pagos.index') }}" class="flex items-center gap-1 sm:gap-1.5 shrink-0">
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                            placeholder="Folio o nombre..." 
                            class="w-28 sm:w-56 md:w-72 rounded-md border-gray-300 shadow-2xs control-fluido shrink-0" required>

                        <button type="submit" title="Buscar usuario o folio" aria-label="Buscar usuario o folio"
                            class="bg-blue-600 hover:bg-blue-700 text-white btn-fluido rounded-md font-semibold shadow-2xs cursor-pointer shrink-0 inline-flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                            </svg>
                        </button>

                        @if($search)
                            <a href="{{ route('pagos.index') }}" title="Limpiar búsqueda" aria-label="Limpiar búsqueda"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 btn-fluido rounded-md font-semibold inline-flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M18 3l3 3-7.5 7.5-3-3L18 3z" />
                                    <path d="M10.5 10.5L5 16c-1.5 1.5-2 3.5-1 5 1.5 1 3.5.5 5-1l5.5-5.5" />
                                    <path d="M6.5 14.5l3 3" />
                                    <path d="M4.5 17.5l2 2" />
                                </svg>
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('pagos.matriz') }}" title="Volver a Matriz General" aria-label="Volver a Matriz General"
                        class="bg-blue-600 hover:bg-blue-700 text-white btn-fluido rounded-md font-semibold shadow-2xs transition inline-flex items-center justify-center gap-1 cursor-pointer shrink-0 whitespace-nowrap">
                        <span class="sm:hidden">Matriz &rarr;</span>
                        <span class="hidden sm:inline">Ver Matriz General &rarr;</span>
                    </a>
                </div>
            </div>

            @if(isset($usuario))
                @php
                    $pagosRealizados = $usuario->pagos->where('estatus', 'pagado');
                    $pagosPendientes = $usuario->pagos->where('estatus', 'pendiente');

                    $totalPagado = $pagosRealizados->sum('monto');
                    $totalPendiente = $pagosPendientes->sum('monto');
                    $totalGeneral = $totalPagado + $totalPendiente;

                    $mesesEs = [
                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                    ];
                    $mesActual = $mesesEs[(int) date('n')] ?? 'Enero';
                @endphp

                <!-- 2. CONTENEDOR PRINCIPAL A DOS COLUMNAS -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-2.5 sm:gap-3 items-start">
                    
                    <!-- ================= COLUMNA IZQUIERDA (4 de 12) ================= -->
                    <div class="lg:col-span-4 bg-gray-50 border border-gray-200 rounded-lg p-2 sm:p-3 shadow-2xs space-y-2 sm:space-y-3">
                        
                        <!-- Datos del Usuario -->
                        <div>
                            <div class="border-b pb-1.5 sm:pb-2 mb-1.5 sm:mb-2.5 flex justify-between items-start gap-2">
                                <div class="min-w-0">
                                    <span class="subtexto-fluido font-bold text-blue-600 uppercase block leading-tight">{{ $usuario->folio }}</span>
                                    <h3 class="titulo-fluido font-bold text-gray-900 truncate">{{ $usuario->nombre_completo }}</h3>
                                </div>
                                <span class="badge-fluido font-bold rounded-full bg-green-100 text-green-800 uppercase shrink-0">
                                    {{ $usuario->estado_servicio }}
                                </span>
                            </div>

                            <div class="space-y-0.5 sm:space-y-1 text-gray-600">
                                <div><span class="font-semibold text-gray-400">Teléfono:</span> {{ $usuario->telefono ?? 'N/D' }}</div>
                                <div><span class="font-semibold text-gray-400">Dirección:</span> {{ $usuario->direccion ?? 'N/D' }}</div>
                                <div><span class="font-semibold text-gray-400">Red:</span> {{ $usuario->red->nombre ?? 'N/D' }}</div>
                                <div><span class="font-semibold text-gray-400">Tarifa Mensual:</span> <span class="font-bold text-blue-600">${{ number_format($usuario->tarifa->monto_mensual ?? 0, 2) }}</span></div>
                            </div>
                        </div>

                        <!-- Resumen de Cuenta con Meses y Adeudos -->
                        <div class="bg-white p-2 sm:p-2.5 rounded border border-gray-200 space-y-1 sm:space-y-1.5 shadow-2xs">
                            <div class="font-semibold text-gray-700 border-b pb-1">Resumen de Cuenta</div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Meses Pagados: <strong class="text-gray-700">({{ $pagosRealizados->count() }})</strong></span> 
                                <span class="font-bold text-green-600">${{ number_format($totalPagado, 2) }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Meses Adeudo: <strong class="text-red-600">({{ $pagosPendientes->count() }})</strong></span> 
                                <span class="font-bold text-red-600">${{ number_format($totalPendiente, 2) }}</span>
                            </div>

                            <div class="flex justify-between items-center border-t pt-1">
                                <span class="font-semibold text-gray-700">Total General:</span> 
                                <span class="font-bold text-gray-900">${{ number_format($totalGeneral, 2) }}</span>
                            </div>
                        </div>

                    </div>

                    <!-- ================= COLUMNA DERECHA (8 de 12) ================= -->
                    <div class="lg:col-span-8 space-y-2.5 sm:space-y-3">
                        
                        <!-- Formulario de Registro de Pago -->
                        <div class="bg-blue-50/60 border border-blue-200 p-2 sm:p-2.5 rounded-lg shadow-2xs w-full sm:w-fit max-w-full overflow-x-auto">
                            <h4 class="titulo-fluido font-bold text-blue-900 uppercase mb-1.5">Registrar Nuevo Cobro</h4>
                            <form action="{{ route('pagos.store') }}" method="POST" class="flex flex-nowrap items-end gap-1.5 sm:gap-2 min-w-max sm:min-w-0">
                                @csrf
                                <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">

                                <div class="flex-1 min-w-[84px] sm:flex-initial sm:w-40 lg:w-44 shrink-0">
                                    <label class="block subtexto-fluido font-medium text-gray-600 mb-0.5">Mes</label>
                                    <select name="periodo_mes" class="w-full rounded border-gray-300 control-fluido leading-normal" required>
                                        @foreach($mesesEs as $mes)
                                            <option value="{{ $mes }}" {{ old('periodo_mes', $mesActual) == $mes ? 'selected' : '' }}>{{ $mes }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="w-14 sm:w-20 shrink-0">
                                    <label class="block subtexto-fluido font-medium text-gray-600 mb-0.5">Año</label>
                                    <input type="number" name="periodo_anio" value="{{ old('periodo_anio', date('Y')) }}" class="w-full rounded border-gray-300 control-fluido leading-normal" required>
                                </div>

                                <div class="w-16 sm:w-24 shrink-0">
                                    <label class="block subtexto-fluido font-medium text-gray-600 mb-0.5">Importe ($)</label>
                                    <input type="number" step="0.01" name="monto" value="{{ old('monto', $usuario->tarifa->monto_mensual ?? 0) }}" class="w-full rounded border-gray-300 control-fluido leading-normal" required>
                                </div>

                                <div class="shrink-0">
                                    <button type="submit" title="Guardar cobro" aria-label="Guardar cobro"
                                        class="bg-blue-600 hover:bg-blue-700 text-white btn-fluido rounded border border-blue-600 font-semibold leading-normal shadow-2xs cursor-pointer inline-flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                            <polyline points="17 21 17 13 7 13 7 21" />
                                            <polyline points="7 3 7 8 15 8" />
                                        </svg>
                                        <span class="hidden sm:inline">Guardar</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Historial de Pagos / Tabla -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden shadow-2xs w-full">
                            <div class="bg-gray-100 px-2.5 py-1.5 border-b flex justify-between items-center gap-2">
                                <h4 class="titulo-fluido font-bold text-gray-700 uppercase truncate">Historial de Recibos</h4>
                                <span class="badge-fluido font-semibold bg-white rounded border text-gray-600 shrink-0">{{ count($usuario->pagos) }} registros</span>
                            </div>
                            <div class="max-h-72 overflow-x-auto overflow-y-auto w-full">
                                <table class="w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="text-left font-medium text-gray-500 uppercase">Periodo</th>
                                            <th class="text-left font-medium text-gray-500 uppercase">Importe</th>
                                            <th class="text-center font-medium text-gray-500 uppercase">Estatus</th>
                                            <th class="hidden sm:table-cell text-left font-medium text-gray-500 uppercase">Fecha de Pago</th>
                                            <th class="text-center font-medium text-gray-500 uppercase">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($usuario->pagos as $pago)
                                            <tr class="hover:bg-gray-50">
                                                <td class="font-medium text-gray-900 whitespace-nowrap">
                                                    {{ $pago->periodo_mes }} {{ $pago->periodo_anio }}
                                                </td>
                                                <td class="text-gray-600 whitespace-nowrap">
                                                    ${{ number_format($pago->monto, 2) }}
                                                </td>
                                                <td class="text-center whitespace-nowrap">
                                                    @if($pago->estatus == 'pagado')
                                                        <span class="badge-fluido font-bold rounded bg-green-100 text-green-800 inline-block">Pagado</span>
                                                    @else
                                                        <span class="badge-fluido font-bold rounded bg-red-100 text-red-800 inline-block">Pendiente</span>
                                                    @endif
                                                </td>
                                                <td class="hidden sm:table-cell text-gray-500 whitespace-nowrap">
                                                    {{ $pago->fecha_pago ? date('d/m/Y H:i', strtotime($pago->fecha_pago)) : '-' }}
                                                </td>
                                                <td class="text-center whitespace-nowrap">
                                                    @if($pago->estatus == 'pendiente')
                                                        <form action="{{ route('pagos.store') }}" method="POST" onsubmit="return confirm('¿Desea registrar el pago de {{ $pago->periodo_mes }} {{ $pago->periodo_anio }} por ${{ number_format($pago->monto, 2) }}?');" class="inline">
                                                            @csrf
                                                            <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
                                                            <input type="hidden" name="periodo_mes" value="{{ $pago->periodo_mes }}">
                                                            <input type="hidden" name="periodo_anio" value="{{ $pago->periodo_anio }}">
                                                            <input type="hidden" name="monto" value="{{ $pago->monto }}">
                                                            <button type="submit" style="background-color: #16a34a; color: #ffffff;" class="btn-fluido rounded font-semibold shadow-2xs cursor-pointer hover:bg-green-700">
                                                                Pagar
                                                            </button>
                                                        </form>
                                                    @else
                                                        <div class="flex items-center justify-center gap-1">
                                                            <!-- Botón Icono Ticket -->
                                                            <a href="{{ route('pagos.ticket', $pago->id) }}" target="_blank" class="btn-fluido bg-gray-700 hover:bg-gray-800 text-white rounded shadow-2xs transition inline-flex items-center justify-center cursor-pointer" title="Imprimir Ticket Térmico">
                                                                🖨️
                                                            </a>

                                                            <!-- Botón Icono Comprobante PDF -->
                                                            <a href="{{ route('pagos.comprobante', $pago->id) }}" target="_blank" class="btn-fluido bg-blue-600 hover:bg-blue-700 text-white rounded shadow-2xs transition inline-flex items-center justify-center cursor-pointer" title="Ver Comprobante Electrónico">
                                                                📄
                                                            </a>

                                                            <!-- Botón Anular -->
                                                            <form action="{{ route('pagos.anular', $pago->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de anular este pago?');" class="inline">
                                                                @csrf
                                                                <button type="submit" class="btn-fluido bg-red-600 hover:bg-red-700 text-white rounded font-semibold shadow-2xs transition cursor-pointer" title="Anular Pago">
                                                                    Anular
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-4 text-center text-gray-400">Sin pagos registrados para este usuario.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    Ingresa un folio o nombre arriba para consultar y cobrar.
                </div>
            @endif

        </div>
    </div>
</x-app-layout>