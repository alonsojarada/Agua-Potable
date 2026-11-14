<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Matriz General de Pagos por Mes') }}
            </h2>
            <a href="{{ route('pagos.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow">
                ← Ir a Caja Individual
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">

                <!-- Alertas -->
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

                <!-- Buscador y Selector de Año -->
                <form method="GET" action="{{ route('pagos.matriz') }}" class="flex gap-2 mb-3 items-center">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                        placeholder="Buscar por Folio o Nombre..." 
                        class="flex-1 rounded-md border-gray-300 shadow-sm text-xs py-1.5">
                    
                    <select name="anio" class="rounded-md border-gray-300 text-xs py-1.5 w-28" onchange="this.form.submit()">
                        @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                            <option value="{{ $i }}" {{ $anioActual == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-md text-xs font-semibold shadow">
                        Buscar
                    </button>
                    @if($search)
                        <a href="{{ route('pagos.matriz') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded-md text-xs font-semibold flex items-center">
                            Limpiar
                        </a>
                    @endif
                </form>

                <!-- INDICADOR VISUAL -->
                <div class="mb-3 flex justify-between items-center bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-md">
                    <span class="text-xs font-bold text-blue-900 uppercase">
                        Mostrando Ejercicio Fiscal / Año: <span class="text-blue-600 text-sm underline">{{ $anioActual }}</span>
                    </span>
                    <span class="text-[11px] text-gray-500">
                        * Verde: Pagado | Rojo: Atrasado | Naranja: Mes Actual | Blanco: Futuro (Haz clic en el usuario para ir a su Caja)
                    </span>
                </div>

                <!-- Tabla Estilo Matriz -->
                <div class="border rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-600 uppercase w-48">Usuario / Toma</th>
                                <th class="px-3 py-2 text-center font-medium text-gray-600 uppercase w-24">Adeudo</th>
                                @foreach($mesesAnio as $mes)
                                    <th class="px-2 py-2 text-center font-medium text-gray-600 uppercase">{{ substr($mes, 0, 3) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $mesesNumero = [
                                    'Enero' => 1, 'Febrero' => 2, 'Marzo' => 3, 'Abril' => 4,
                                    'Mayo' => 5, 'Junio' => 6, 'Julio' => 7, 'Agosto' => 8,
                                    'Septiembre' => 9, 'Octubre' => 10, 'Noviembre' => 11, 'Diciembre' => 12
                                ];
                                $mesActualNum = (int)\Carbon\Carbon::now()->month;
                                $anioEnCurso = (int)\Carbon\Carbon::now()->year;
                            @endphp

                            @forelse($usuarios as $usuario)
                                @php
                                    $montoTarifa = $usuario->tarifa->monto_mensual ?? 0;
                                    $adeudoTotal = 0;

                                    foreach($mesesAnio as $mNombre) {
                                        $mNum = $mesesNumero[$mNombre];
                                        $pagoM = $usuario->pagos->first(fn($p) => trim(strtolower($p->periodo_mes)) == trim(strtolower($mNombre)) && (int)$p->periodo_anio == (int)$anioActual);
                                        $pagado = $pagoM && strtolower($pagoM->estatus) == 'pagado';

                                        if (!$pagado) {
                                            if ((int)$anioActual < $anioEnCurso || ((int)$anioActual == $anioEnCurso && $mNum <= $mesActualNum)) {
                                                $adeudoTotal += $montoTarifa;
                                            }
                                        }
                                    }
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    <!-- Columna de Usuario (Con enlace directo a la Caja Individual filtrada por su folio) -->
                                    <td class="px-3 py-2">
                                        <a href="{{ route('pagos.index', ['search' => $usuario->folio]) }}" class="group block" title="Ir a Caja Individual de {{ $usuario->nombre_completo }}">
                                            <span class="font-bold text-blue-600 group-hover:underline uppercase">{{ $usuario->folio }}</span>
                                            <div class="font-semibold text-gray-900 group-hover:text-blue-600 truncate max-w-[180px]" title="{{ $usuario->nombre_completo }}">
                                                {{ $usuario->nombre_completo }}
                                            </div>
                                        </a>
                                    </td>

                                    <!-- Columna de Adeudo Total -->
                                    <td class="px-3 py-2 text-center font-bold {{ $adeudoTotal > 0 ? 'text-red-600 bg-red-50/50' : 'text-green-600' }}">
                                        ${{ number_format($adeudoTotal, 2) }}
                                    </td>

                                    <!-- Columnas de los 12 Meses -->
                                    @foreach($mesesAnio as $mNombre)
                                        @php
                                            $mNum = (int)$mesesNumero[$mNombre];
                                            $pagoMes = $usuario->pagos->first(fn($p) => trim(strtolower($p->periodo_mes)) == trim(strtolower($mNombre)) && (int)$p->periodo_anio == (int)$anioActual);
                                            $pagado = $pagoMes && strtolower($pagoMes->estatus) == 'pagado';

                                            if ($pagado) {
                                                $bgClass = 'bg-green-500 hover:bg-green-600 border-green-500 text-white font-bold shadow-sm cursor-pointer';
                                                $simbolo = '✓';
                                            } else {
                                                if ((int)$anioActual < $anioEnCurso) {
                                                    $bgClass = 'bg-red-500 hover:bg-red-600 border-red-500 text-white font-bold shadow-sm cursor-pointer';
                                                    $simbolo = '$';
                                                } elseif ((int)$anioActual > $anioEnCurso) {
                                                    $bgClass = 'bg-white hover:bg-gray-100 border-gray-200 text-gray-400 font-normal shadow-sm cursor-pointer';
                                                    $simbolo = '$';
                                                } else {
                                                    if ($mNum < $mesActualNum) {
                                                        $bgClass = 'bg-red-500 hover:bg-red-600 border-red-500 text-white font-bold shadow-sm cursor-pointer';
                                                        $simbolo = '$';
                                                    } elseif ($mNum == $mesActualNum) {
                                                        $bgClass = 'bg-amber-500 hover:bg-amber-600 border-amber-500 text-white font-bold shadow-sm cursor-pointer';
                                                        $simbolo = '$';
                                                    } else {
                                                        $bgClass = 'bg-white hover:bg-gray-100 border-gray-200 text-gray-400 font-normal shadow-sm cursor-pointer';
                                                        $simbolo = '$';
                                                    }
                                                }
                                            }
                                        @endphp

                                        <td class="px-1.5 py-2 text-center">
                                            @if($pagado)
                                                <div class="w-full py-1 border {{ $bgClass }} rounded text-xs flex items-center justify-center cursor-default" title="{{ $mNombre }} - Pagado">
                                                    {{ $simbolo }}
                                                </div>
                                            @else
                                                <form action="{{ route('pagos.store') }}" method="POST" onsubmit="return confirm('¿Desea registrar el cobro de {{ $mNombre }} {{ $anioActual }} para {{ $usuario->nombre_completo }}?');">
                                                    @csrf
                                                    <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
                                                    <input type="hidden" name="periodo_mes" value="{{ $mNombre }}">
                                                    <input type="hidden" name="periodo_anio" value="{{ $anioActual }}">
                                                    <input type="hidden" name="monto" value="{{ $montoTarifa }}">
                                                    
                                                    <button type="submit" class="w-full py-1 border {{ $bgClass }} rounded text-xs transition flex items-center justify-center" title="Cobrar {{ $mNombre }}">
                                                        {{ $simbolo }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="px-3 py-6 text-center text-gray-400">
                                        No se encontraron usuarios registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if(method_exists($usuarios, 'links'))
                    <div class="mt-4">
                        {{ $usuarios->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>