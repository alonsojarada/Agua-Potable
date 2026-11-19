<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center min-w-0">
            <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight truncate min-w-0" title="Matriz General de Pagos por Mes">
                {{ __('Vista Global de Pagos por Mes') }}
            </h2>
        </div>
    </x-slot>

    <style>
        @media (max-width: 639px) {
            .matriz-movil-compacta table th,
            .matriz-movil-compacta table td {
                font-size: clamp(0.56rem, 2.2vw, 0.68rem);
            }
            .matriz-movil-compacta .celda-mes-btn {
                font-size: 0.6rem;
            }
            .matriz-movil-compacta .folio-toma {
                display: none !important;
            }
            .matriz-movil-compacta #col-fija-usuario {
                width: 5.5rem;
            }
            .matriz-movil-compacta .nombre-usuario-celda {
                max-width: 5rem;
            }
        }
    </style>

    <!-- CONTENEDOR DE ANCHO COMPLETO Y DINÁMICO -->
    <div class="py-1.5 sm:py-2 px-1.5 sm:px-3 w-full matriz-movil-compacta">
        <div class="bg-white overflow-hidden shadow-2xs rounded-lg p-2 sm:p-3 w-full">

            <!-- Alertas -->
            @if (session('success'))
                <div class="mb-2 sm:mb-3 bg-green-100 border border-green-400 text-green-700 px-3 py-1.5 rounded text-xs">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-2 sm:mb-3 bg-red-100 border border-red-400 text-red-700 px-3 py-1.5 rounded text-xs">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- BARRA SUPERIOR: BUSCADOR + AÑO + BOTÓN EN UNA SOLA FILA -->
            <div class="overflow-x-auto mb-2.5 sm:mb-3 bg-slate-50 p-1.5 sm:p-2.5 rounded-md border border-gray-200">
                <div class="flex flex-row items-center justify-between gap-1.5 sm:gap-2 min-w-max sm:min-w-0">
                    <form method="GET" action="{{ route('pagos.matriz') }}" class="flex items-center gap-1 sm:gap-1.5 shrink-0">
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                            placeholder="Folio o nombre..." 
                            class="w-24 sm:w-48 md:w-64 rounded-md border-gray-300 shadow-2xs text-[10px] sm:text-xs py-1 sm:py-1.5 px-2 sm:px-2.5 shrink-0">
                        
                        <select name="anio" aria-label="Año" class="rounded-md border-gray-300 text-[10px] sm:text-xs py-1 sm:py-1.5 px-1.5 sm:px-2 w-16 sm:w-24 shrink-0" onchange="this.form.submit()">
                            @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                                <option value="{{ $i }}" {{ $anioActual == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>

                        <button type="submit" title="Buscar usuario o folio" aria-label="Buscar usuario o folio"
                            class="bg-blue-600 hover:bg-blue-700 text-white p-1.5 sm:px-3 sm:py-1.5 rounded-md text-[10px] sm:text-xs font-semibold shadow-2xs cursor-pointer shrink-0 inline-flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                            </svg>
                        </button>

                        @if($search)
                            <a href="{{ route('pagos.matriz') }}" title="Limpiar búsqueda" aria-label="Limpiar búsqueda"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 p-1.5 sm:px-2.5 sm:py-1.5 rounded-md text-[10px] sm:text-xs font-semibold inline-flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M18 3l3 3-7.5 7.5-3-3L18 3z" />
                                    <path d="M10.5 10.5L5 16c-1.5 1.5-2 3.5-1 5 1.5 1 3.5.5 5-1l5.5-5.5" />
                                    <path d="M6.5 14.5l3 3" />
                                    <path d="M4.5 17.5l2 2" />
                                </svg>
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('pagos.index') }}" title="Ir a Caja Individual" aria-label="Ir a Caja Individual"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-2 py-1 sm:px-3.5 sm:py-1.5 rounded-md text-[10px] sm:text-xs font-semibold shadow-2xs transition inline-flex items-center justify-center gap-1 cursor-pointer shrink-0 whitespace-nowrap">
                        <span class="sm:hidden">&larr; Caja</span>
                        <span class="hidden sm:inline">&larr; Ir a Caja Individual</span>
                    </a>
                </div>
            </div>

            <!-- INDICADOR VISUAL -->
            <div class="mb-2.5 sm:mb-3 flex justify-between items-center bg-blue-50 border border-blue-200 px-2.5 sm:px-3 py-1.5 rounded-md text-[10px] sm:text-xs">
                <span class="font-bold text-blue-900 uppercase">
                    Mostrando Año: <span class="text-blue-600 text-xs sm:text-sm underline">{{ $anioActual }}</span>
                </span>
                <span class="text-[10px] sm:text-[11px] text-gray-500 hidden sm:inline">
                    * Verde: Pagado | Rojo: Atrasado | Naranja: Mes Actual | Blanco: Futuro (Haz clic en el usuario para ir a su Caja)
                </span>
            </div>

            @php
                $mesesNumero = [
                    'Enero' => 1, 'Febrero' => 2, 'Marzo' => 3, 'Abril' => 4,
                    'Mayo' => 5, 'Junio' => 6, 'Julio' => 7, 'Agosto' => 8,
                    'Septiembre' => 9, 'Octubre' => 10, 'Noviembre' => 11, 'Diciembre' => 12
                ];
                $mesActualNum = (int)\Carbon\Carbon::now()->month;
                $anioEnCurso = (int)\Carbon\Carbon::now()->year;
            @endphp

            <!-- Tabla Estilo Matriz (Con tamaño de fuente sutilmente ajustado) -->
            <div id="contenedor-matriz-scroll" class="border rounded-lg overflow-x-auto shadow-2xs">
                <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-xs lg:text-[13.5px] border-collapse">
                    <thead class="bg-gray-100 sticky top-0 z-20">
                        <tr>
                            <!-- Encabezado Fijo a la Izquierda -->
                            <th id="col-fija-usuario" class="px-3 py-2.5 text-left font-medium text-gray-600 uppercase w-52 sticky left-0 z-30 bg-gray-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] text-xs sm:text-xs lg:text-xs">Usuario / Toma</th>
                            <th id="col-fija-adeudo" class="px-3 py-2.5 text-center font-medium text-gray-600 uppercase w-28 text-xs sm:text-xs lg:text-xs">Adeudo</th>
                            @foreach($mesesAnio as $mes)
                                @php
                                    $esMesActualHeader = ((int)($mesesNumero[$mes] ?? 0) === $mesActualNum);
                                @endphp
                                <th id="{{ $esMesActualHeader ? 'mes-actual-header' : '' }}" {{ $esMesActualHeader ? 'data-mes-actual="1"' : '' }} class="px-2.5 py-2.5 text-center font-medium uppercase text-xs sm:text-xs lg:text-xs {{ $esMesActualHeader ? 'text-amber-700 bg-amber-100/80 font-bold' : 'text-gray-600' }}">
                                    {{ substr($mes, 0, 3) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
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
                                <!-- Columna Fija de Usuario a la Izquierda -->
                                <td class="px-3 py-2.5 sticky left-0 z-10 bg-white shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                    <a href="{{ route('pagos.index', ['search' => $usuario->folio]) }}" class="group block" title="Ir a Caja Individual de {{ $usuario->nombre_completo }}">
                                        <span class="folio-toma font-bold text-blue-600 group-hover:underline uppercase text-xs sm:text-xs lg:text-[13.5px]">{{ $usuario->folio }}</span>
                                        <div class="nombre-usuario-celda font-semibold text-gray-900 group-hover:text-blue-600 truncate max-w-[200px] text-xs sm:text-xs lg:text-[13.5px]" title="{{ $usuario->nombre_completo }}">
                                            {{ $usuario->nombre_completo }}
                                        </div>
                                    </a>
                                </td>

                                <!-- Columna de Adeudo Total -->
                                <td class="px-3 py-2.5 text-center font-bold {{ $adeudoTotal > 0 ? 'text-red-600 bg-red-50/50' : 'text-green-600' }}">
                                    ${{ number_format($adeudoTotal, 2) }}
                                </td>

                                <!-- Columnas de los 12 Meses -->
                                @foreach($mesesAnio as $mNombre)
                                    @php
                                        $mNum = (int)$mesesNumero[$mNombre];
                                        $pagoMes = $usuario->pagos->first(fn($p) => trim(strtolower($p->periodo_mes)) == trim(strtolower($mNombre)) && (int)$p->periodo_anio == (int)$anioActual);
                                        $pagado = $pagoMes && strtolower($pagoMes->estatus) == 'pagado';

                                        if ($pagado) {
                                            $bgClass = 'bg-green-500 hover:bg-green-600 border-green-500 text-white font-bold shadow-2xs cursor-pointer';
                                            $simbolo = '✓';
                                        } else {
                                            if ((int)$anioActual < $anioEnCurso) {
                                                $bgClass = 'bg-red-500 hover:bg-red-600 border-red-500 text-white font-bold shadow-2xs cursor-pointer';
                                                $simbolo = '$';
                                            } elseif ((int)$anioActual > $anioEnCurso) {
                                                $bgClass = 'bg-white hover:bg-gray-100 border-gray-200 text-gray-400 font-normal shadow-2xs cursor-pointer';
                                                $simbolo = '$';
                                            } else {
                                                if ($mNum < $mesActualNum) {
                                                    $bgClass = 'bg-red-500 hover:bg-red-600 border-red-500 text-white font-bold shadow-2xs cursor-pointer';
                                                    $simbolo = '$';
                                                } elseif ($mNum == $mesActualNum) {
                                                    $bgClass = 'bg-amber-500 hover:bg-amber-600 border-amber-500 text-white font-bold shadow-2xs cursor-pointer';
                                                    $simbolo = '$';
                                                } else {
                                                    $bgClass = 'bg-white hover:bg-gray-100 border-gray-200 text-gray-400 font-normal shadow-2xs cursor-pointer';
                                                    $simbolo = '$';
                                                }
                                            }
                                        }
                                    @endphp

                                    <td id="{{ ($loop->parent->first && $mNum === $mesActualNum) ? 'mes-actual-celda' : '' }}" class="px-1.5 py-2.5 text-center">
                                        @if($pagado)
                                            <div class="w-full py-1.5 border {{ $bgClass }} rounded text-xs sm:text-xs lg:text-[13.5px] flex items-center justify-center cursor-default celda-mes-btn" title="{{ $mNombre }} - Pagado">
                                                {{ $simbolo }}
                                            </div>
                                        @else
                                            <form action="{{ route('pagos.store') }}" method="POST" onsubmit="return confirm('¿Desea registrar el cobro de {{ $mNombre }} {{ $anioActual }} para {{ $usuario->nombre_completo }}?');">
                                                @csrf
                                                <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">
                                                <input type="hidden" name="periodo_mes" value="{{ $mNombre }}">
                                                <input type="hidden" name="periodo_anio" value="{{ $anioActual }}">
                                                <input type="hidden" name="monto" value="{{ $montoTarifa }}">
                                                
                                                <button type="submit" class="w-full py-1.5 border {{ $bgClass }} rounded text-xs sm:text-xs lg:text-[13.5px] transition flex items-center justify-center cursor-pointer celda-mes-btn" title="Cobrar {{ $mNombre }}">
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
                <div class="mt-3">
                    {{ $usuarios->links() }}
                </div>
            @endif

        </div>
    </div>

    <script>
        (function () {
            let movidoPorUsuario = false;

            function irAlMesActual() {
                if (movidoPorUsuario) return;

                const objetivo = document.getElementById("mes-actual-celda") || document.getElementById("mes-actual-header");
                const contenedor = document.getElementById("contenedor-matriz-scroll");
                if (!objetivo) return;

                const esMovil = window.innerWidth < 640;

                if (contenedor && contenedor.scrollWidth > contenedor.clientWidth) {
                    const colFija = document.getElementById("col-fija-usuario");
                    const anchoFijo = colFija ? colFija.getBoundingClientRect().width : 0;
                    const rectCont = contenedor.getBoundingClientRect();
                    const rectObj = objetivo.getBoundingClientRect();

                    const posEnContenedor = (rectObj.left - rectCont.left) + contenedor.scrollLeft;
                    const margenContexto = esMovil ? 0 : Math.max(0, rectObj.width * 2);
                    const destino = Math.max(0, posEnContenedor - anchoFijo - margenContexto);

                    contenedor.scrollLeft = destino;
                }

                objetivo.scrollIntoView({
                    behavior: "auto",
                    block: "nearest",
                    inline: esMovil ? "nearest" : "center"
                });
            }

            document.addEventListener("DOMContentLoaded", function () {
                const contenedor = document.getElementById("contenedor-matriz-scroll");
                const cancelarAuto = () => { movidoPorUsuario = true; };

                if (contenedor) {
                    contenedor.addEventListener("wheel", cancelarAuto, { passive: true });
                    contenedor.addEventListener("touchstart", cancelarAuto, { passive: true });
                }
                window.addEventListener("wheel", cancelarAuto, { passive: true });
                window.addEventListener("touchstart", cancelarAuto, { passive: true });

                [0, 100, 300, 700].forEach(ms => setTimeout(irAlMesActual, ms));
            });

            window.addEventListener("load", irAlMesActual);
        })();
    </script>
</x-app-layout>