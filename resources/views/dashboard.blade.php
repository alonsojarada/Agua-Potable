<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center min-w-0">
            <h2 class="font-semibold text-xs sm:text-sm md:text-base lg:text-xl text-gray-800 leading-tight truncate min-w-0" title="Resumen General - Sistema de Agua">
                {{ __('Estatus General') }}
            </h2>
        </div>
    </x-slot>

    <style>
        .dashboard-fluido {
            font-size: clamp(0.66rem, 1.15vw, 0.875rem);
        }
        .dashboard-fluido .titulo-seccion {
            font-size: clamp(0.74rem, 1.3vw, 1rem);
        }
        .dashboard-fluido .titulo-tarjeta {
            font-size: clamp(0.64rem, 1.05vw, 0.8rem);
        }
        .dashboard-fluido .cifra-principal {
            font-size: clamp(1.05rem, 2vw, 1.55rem);
        }
        .dashboard-fluido .monto-destacado {
            font-size: clamp(0.8rem, 1.4vw, 1.1rem);
        }
        .dashboard-fluido .detalle-fluido {
            font-size: clamp(0.58rem, 0.95vw, 0.76rem);
        }
        .dashboard-fluido .contenedor-scroll-completo {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        @media (max-width: 639px) {
            .dashboard-fluido .tarjeta-bloque {
                padding: 0.625rem;
            }
            .dashboard-fluido .subtarjeta-bloque {
                padding: 0.55rem;
            }
            .dashboard-fluido .cifra-principal {
                font-size: clamp(1rem, 4.2vw, 1.2rem);
            }
            .dashboard-fluido .monto-destacado {
                font-size: clamp(0.76rem, 3.3vw, 0.92rem);
            }
            .dashboard-fluido .contenido-contenedor-scroll {
                min-width: 17.5rem;
            }
        }
    </style>

    @php
        $mesesEs = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $mesesNumeroMapa = [
            'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4,
            'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8,
            'septiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12
        ];

        $mesActualNum = (int) \Carbon\Carbon::now()->month;
        $anioActualResumen = (int) \Carbon\Carbon::now()->year;
        $mesNombreActual = $mesesEs[$mesActualNum] ?? 'Noviembre';

        // Mes actual (solo este mes)
        $calcTotalUsuarios = $totalUsuarios ?? 0;
        $calcUsuariosActivos = $usuariosActivos ?? 0;
        $calcPagadosMesCount = $pagadosMesActualCount ?? 0;
        $calcPagadosMesMonto = $pagadosMesActualMonto ?? 0;
        $calcAdeudoMesCount = $adeudoMesActualCount ?? 0;
        $calcAdeudoMesMonto = $adeudoMesActualMonto ?? 0;

        // Global (meses atrás + mes actual)
        $calcGlobalAdeudoCount = $globalAdeudoCount ?? ($adeudoAnteriorCount ?? 0);
        $calcGlobalAdeudoMeses = $globalAdeudoMeses ?? ($adeudoAnteriorMeses ?? 0);
        $calcGlobalAdeudoMonto = $globalAdeudoMonto ?? ($adeudoAnteriorMonto ?? 0);

        $calcGlobalCorrienteCount = $globalCorrienteCount ?? ($alCorrienteCount ?? 0);
        $calcGlobalCorrienteMeses = $globalCorrienteMeses ?? ($alCorrienteMeses ?? 0);
        $calcGlobalCorrienteMonto = $globalCorrienteMonto ?? ($alCorrienteMonto ?? 0);

        $sinDatosExternos = (
            $calcTotalUsuarios === 0 &&
            $calcPagadosMesCount === 0 &&
            $calcAdeudoMesCount === 0 &&
            $calcGlobalAdeudoCount === 0 &&
            $calcGlobalCorrienteCount === 0
        );

        if ($sinDatosExternos) {
            $modeloUsuario = class_exists(\App\Models\Usuario::class)
                ? \App\Models\Usuario::class
                : (class_exists(\App\Models\User::class) ? \App\Models\User::class : null);

            if ($modeloUsuario) {
                $queryUsuarios = $modeloUsuario::query();
                try {
                    $queryUsuarios->with(['tarifa', 'pagos']);
                } catch (\Throwable $e) {
                    try {
                        $queryUsuarios->with('pagos');
                    } catch (\Throwable $e2) {}
                }

                $listaUsuarios = $queryUsuarios->get();
                $calcTotalUsuarios = $listaUsuarios->count();

                $activosFiltrados = $listaUsuarios->filter(function ($u) {
                    if (isset($u->activo)) {
                        return (bool) $u->activo;
                    }
                    if (isset($u->estatus)) {
                        $est = strtolower(trim((string) $u->estatus));
                        return in_array($est, ['activo', 'activa', '1', 'true', 'habilitado'], true);
                    }
                    if (isset($u->estado)) {
                        $est = strtolower(trim((string) $u->estado));
                        return in_array($est, ['activo', 'activa', '1', 'true', 'habilitado'], true);
                    }
                    return true;
                });
                $calcUsuariosActivos = $activosFiltrados->count();

                $calcPagadosMesCount = 0;
                $calcPagadosMesMonto = 0;
                $calcAdeudoMesCount = 0;
                $calcAdeudoMesMonto = 0;

                $calcGlobalAdeudoCount = 0;
                $calcGlobalAdeudoMeses = 0;
                $calcGlobalAdeudoMonto = 0;

                $calcGlobalCorrienteCount = 0;
                $calcGlobalCorrienteMeses = 0;
                $calcGlobalCorrienteMonto = 0;

                foreach ($listaUsuarios as $u) {
                    $tarifaMensual = (float) ($u->tarifa->monto_mensual ?? $u->monto_mensual ?? 0);
                    $pagosUsuario = collect($u->pagos ?? []);

                    // 1. SOLO MES ACTUAL
                    $pagoActual = $pagosUsuario->first(function ($p) use ($mesActualNum, $anioActualResumen, $mesesNumeroMapa) {
                        $mRaw = strtolower(trim((string) ($p->periodo_mes ?? '')));
                        $mNum = $mesesNumeroMapa[$mRaw] ?? (int) $mRaw;
                        $aPago = (int) ($p->periodo_anio ?? 0);
                        return $mNum === $mesActualNum && ($aPago === 0 || $aPago === $anioActualResumen);
                    });
                    $pagadoMesActual = $pagoActual && strtolower(trim((string) ($pagoActual->estatus ?? ''))) === 'pagado';

                    if ($pagadoMesActual) {
                        $calcPagadosMesCount++;
                        $montoCobradoMes = (float) ($pagoActual->monto ?? 0);
                        $calcPagadosMesMonto += ($montoCobradoMes > 0 ? $montoCobradoMes : $tarifaMensual);
                    } else {
                        $calcAdeudoMesCount++;
                        $montoPendienteMes = (float) ($pagoActual->monto ?? 0);
                        $calcAdeudoMesMonto += ($montoPendienteMes > 0 ? $montoPendienteMes : $tarifaMensual);
                    }

                    // 2. GLOBAL (MESES ATRÁS GENERADOS + MES ACTUAL)
                    $pagosHastaActual = $pagosUsuario->filter(function ($p) use ($mesActualNum, $anioActualResumen, $mesesNumeroMapa) {
                        $mRaw = strtolower(trim((string) ($p->periodo_mes ?? '')));
                        $mNum = $mesesNumeroMapa[$mRaw] ?? (int) $mRaw;
                        $aPago = (int) ($p->periodo_anio ?? 0);

                        if ($mNum <= 0) {
                            return false;
                        }
                        if ($aPago > 0 && $aPago < $anioActualResumen) {
                            return true;
                        }
                        if ($aPago === 0 || $aPago === $anioActualResumen) {
                            return $mNum <= $mesActualNum;
                        }
                        return false;
                    });

                    $pendientesGlobalUsuario = $pagosHastaActual->filter(function ($p) {
                        return strtolower(trim((string) ($p->estatus ?? ''))) !== 'pagado';
                    });

                    $pagadosGlobalUsuario = $pagosHastaActual->filter(function ($p) {
                        return strtolower(trim((string) ($p->estatus ?? ''))) === 'pagado';
                    });

                    $tieneAdeudoMesActual = !$pagadoMesActual;
                    $pendientesAnterioresCount = $pendientesGlobalUsuario->filter(function ($p) use ($mesActualNum, $anioActualResumen, $mesesNumeroMapa) {
                        $mRaw = strtolower(trim((string) ($p->periodo_mes ?? '')));
                        $mNum = $mesesNumeroMapa[$mRaw] ?? (int) $mRaw;
                        $aPago = (int) ($p->periodo_anio ?? 0);
                        return !($mNum === $mesActualNum && ($aPago === 0 || $aPago === $anioActualResumen));
                    })->count();

                    $mesesPendientesGlobal = $pendientesAnterioresCount + ($tieneAdeudoMesActual ? 1 : 0);

                    if ($mesesPendientesGlobal > 0) {
                        $calcGlobalAdeudoCount++;
                        $calcGlobalAdeudoMeses += $mesesPendientesGlobal;

                        foreach ($pendientesGlobalUsuario as $pPend) {
                            $mPend = (float) ($pPend->monto ?? 0);
                            $calcGlobalAdeudoMonto += ($mPend > 0 ? $mPend : $tarifaMensual);
                        }
                        if (!$pagoActual) {
                            $calcGlobalAdeudoMonto += $tarifaMensual;
                        }
                    } else {
                        $calcGlobalCorrienteCount++;
                        $calcGlobalCorrienteMeses += $pagadosGlobalUsuario->count();

                        foreach ($pagadosGlobalUsuario as $pPag) {
                            $mPag = (float) ($pPag->monto ?? 0);
                            $calcGlobalCorrienteMonto += ($mPag > 0 ? $mPag : $tarifaMensual);
                        }
                    }
                }
            }
        }

        if ($calcUsuariosActivos === 0 && $calcTotalUsuarios > 0) {
            $calcUsuariosActivos = $calcTotalUsuarios;
        }

        $baseUsuarios = max($calcUsuariosActivos, $calcTotalUsuarios);
        $pctPagadosMes = $baseUsuarios > 0 ? round(($calcPagadosMesCount / $baseUsuarios) * 100) : 0;
        $pctAdeudoMes = $baseUsuarios > 0 ? round(($calcAdeudoMesCount / $baseUsuarios) * 100) : 0;

        $pctGlobalCorriente = $baseUsuarios > 0 ? round(($calcGlobalCorrienteCount / $baseUsuarios) * 100) : 0;
        $pctGlobalAdeudo = $baseUsuarios > 0 ? round(($calcGlobalAdeudoCount / $baseUsuarios) * 100) : 0;
    @endphp

    <div class="py-1.5 sm:py-2 px-1.5 sm:px-3 w-full dashboard-fluido contenedor-scroll-completo">
        <div class="w-full space-y-2 sm:space-y-2.5 min-w-0">

            <!-- ETIQUETA SUPERIOR IZQUIERDA: TOTAL USUARIOS + ACCESOS RÁPIDOS -->
            <div class="flex flex-wrap items-center justify-between gap-1.5 sm:gap-2 min-w-0">
                <div class="bg-white border-2 border-blue-600 rounded-lg px-2.5 sm:px-3.5 py-1 sm:py-1.5 shadow-2xs flex items-center gap-1.5 sm:gap-2 min-w-0">
                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="font-extrabold text-blue-950 uppercase titulo-tarjeta whitespace-nowrap">Total Usuarios:</span>
                    <span class="monto-destacado font-extrabold text-blue-700 tabular-nums whitespace-nowrap">{{ number_format($calcUsuariosActivos) }}</span>
                    <span class="detalle-fluido text-gray-500 font-semibold whitespace-nowrap">activos</span>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <a href="{{ route('pagos.index') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-2 sm:px-3 py-1 rounded-md detalle-fluido font-semibold shadow-2xs transition whitespace-nowrap">
                        Ir a Caja
                    </a>
                    <a href="{{ route('pagos.matriz') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-2 sm:px-3 py-1 rounded-md detalle-fluido font-semibold shadow-2xs transition whitespace-nowrap">
                        Ver Matriz
                    </a>
                </div>
            </div>

            <!-- DOS COLUMNAS: IZQUIERDA = MES ACTUAL (SOLO ESTE MES) | DERECHA = GLOBAL (MESES ATRÁS Y MES ACTUAL) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 sm:gap-3 items-stretch min-w-0">

                <!-- COLUMNA IZQUIERDA: MES ACTUAL (SOLO ESTE MES) -->
                <div class="bg-white border-2 border-blue-500/80 rounded-lg tarjeta-bloque sm:p-4 shadow-2xs contenedor-scroll-completo min-w-0">
                    <div class="contenido-contenedor-scroll flex flex-col justify-between space-y-2.5 h-full">
                        <div class="space-y-2.5">
                            <div class="flex flex-wrap items-center justify-between gap-1.5 pb-1.5 border-b border-gray-100">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-blue-600 shrink-0"></span>
                                    <h3 class="font-extrabold text-blue-950 uppercase titulo-seccion">
                                        Mes Actual <span class="text-blue-600">({{ $mesNombreActual }})</span>
                                    </h3>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold detalle-fluido shrink-0">
                                    Solo {{ $mesNombreActual }}
                                </span>
                            </div>

                            <!-- Gráfica visual solo del mes actual -->
                            <div class="bg-slate-50 border border-slate-200 rounded-md p-2 sm:p-2.5">
                                <div class="flex items-center justify-between gap-2 detalle-fluido mb-1">
                                    <span class="font-semibold text-gray-700">Cobro del mes ({{ $mesNombreActual }})</span>
                                    <span class="font-bold text-emerald-700 tabular-nums shrink-0">{{ $pctPagadosMes }}% pagado</span>
                                </div>
                                <div class="w-full h-2.5 sm:h-3 bg-gray-200 rounded-full overflow-hidden flex">
                                    <div class="bg-emerald-500 h-full transition-all duration-300" style="width: {{ $pctPagadosMes }}%"></div>
                                    <div class="bg-amber-400 h-full transition-all duration-300" style="width: {{ $pctAdeudoMes }}%"></div>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-1 detalle-fluido text-gray-600 mt-1.5">
                                    <span class="inline-flex items-center gap-1 whitespace-nowrap">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        Pagados: <strong class="tabular-nums">{{ number_format($calcPagadosMesCount) }}</strong>
                                    </span>
                                    <span class="inline-flex items-center gap-1 whitespace-nowrap">
                                        <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                                        Con adeudo: <strong class="tabular-nums">{{ number_format($calcAdeudoMesCount) }}</strong>
                                    </span>
                                </div>
                            </div>

                            <!-- Tarjetas solo del mes actual -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-2.5">
                                <div class="border border-emerald-200 bg-emerald-50/40 rounded-lg subtarjeta-bloque sm:p-3 flex flex-col justify-between">
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <span class="font-bold text-emerald-800 uppercase titulo-tarjeta">Ya Pagaron</span>
                                        <span class="px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold detalle-fluido tabular-nums shrink-0">{{ $pctPagadosMes }}%</span>
                                    </div>
                                    <div class="cifra-principal font-extrabold text-emerald-600 leading-tight tabular-nums whitespace-nowrap">
                                        {{ number_format($calcPagadosMesCount) }} <span class="detalle-fluido font-semibold text-gray-600">usuarios</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-emerald-100 rounded-full overflow-hidden my-1.5">
                                        <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $pctPagadosMes }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between gap-1 detalle-fluido pt-1 border-t border-emerald-100">
                                        <span class="text-gray-600 shrink-0">Cobrado:</span>
                                        <span class="monto-destacado font-extrabold text-emerald-700 tabular-nums whitespace-nowrap text-right">${{ number_format($calcPagadosMesMonto, 2) }}</span>
                                    </div>
                                </div>

                                <div class="border border-amber-200 bg-amber-50/40 rounded-lg subtarjeta-bloque sm:p-3 flex flex-col justify-between">
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <span class="font-bold text-amber-800 uppercase titulo-tarjeta">Con Adeudo</span>
                                        <span class="px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold detalle-fluido tabular-nums shrink-0">{{ $pctAdeudoMes }}%</span>
                                    </div>
                                    <div class="cifra-principal font-extrabold text-amber-600 leading-tight tabular-nums whitespace-nowrap">
                                        {{ number_format($calcAdeudoMesCount) }} <span class="detalle-fluido font-semibold text-gray-600">usuarios</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-amber-100 rounded-full overflow-hidden my-1.5">
                                        <div class="bg-amber-500 h-full rounded-full" style="width: {{ $pctAdeudoMes }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between gap-1 detalle-fluido pt-1 border-t border-amber-100">
                                        <span class="text-gray-600 shrink-0">Por cobrar:</span>
                                        <span class="monto-destacado font-extrabold text-amber-700 tabular-nums whitespace-nowrap text-right">${{ number_format($calcAdeudoMesMonto, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-1.5 border-t border-gray-100 flex flex-wrap items-center justify-between gap-1 detalle-fluido text-gray-500">
                            <span>Activos: <strong class="tabular-nums">{{ number_format($calcUsuariosActivos) }}</strong></span>
                            <span>Periodo: <strong>{{ $mesNombreActual }} {{ $anioActualResumen }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- COLUMNA DERECHA: GLOBAL (MESES ATRÁS Y MES ACTUAL) -->
                <div class="bg-white border-2 border-slate-400/80 rounded-lg tarjeta-bloque sm:p-4 shadow-2xs contenedor-scroll-completo min-w-0">
                    <div class="contenido-contenedor-scroll flex flex-col justify-between space-y-2.5 h-full">
                        <div class="space-y-2.5">
                            <div class="flex flex-wrap items-center justify-between gap-1.5 pb-1.5 border-b border-gray-100">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-slate-700 shrink-0"></span>
                                    <h3 class="font-extrabold text-slate-900 uppercase titulo-seccion">
                                        Global <span class="text-slate-600">(Meses Atrás y Mes Actual)</span>
                                    </h3>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold detalle-fluido tabular-nums shrink-0">
                                    {{ $pctGlobalCorriente }}% al corriente
                                </span>
                            </div>

                            <!-- Gráfica visual global -->
                            <div class="bg-slate-50 border border-slate-200 rounded-md p-2 sm:p-2.5">
                                <div class="flex items-center justify-between gap-2 detalle-fluido mb-1">
                                    <span class="font-semibold text-gray-700">Estado global (meses atrás + {{ $mesNombreActual }})</span>
                                    <span class="font-bold text-indigo-700 tabular-nums shrink-0">{{ $pctGlobalCorriente }}% al corriente</span>
                                </div>
                                <div class="w-full h-2.5 sm:h-3 bg-gray-200 rounded-full overflow-hidden flex">
                                    <div class="bg-indigo-500 h-full transition-all duration-300" style="width: {{ $pctGlobalCorriente }}%"></div>
                                    <div class="bg-red-500 h-full transition-all duration-300" style="width: {{ $pctGlobalAdeudo }}%"></div>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-1 detalle-fluido text-gray-600 mt-1.5">
                                    <span class="inline-flex items-center gap-1 whitespace-nowrap">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 shrink-0"></span>
                                        Al corriente: <strong class="tabular-nums">{{ number_format($calcGlobalCorrienteCount) }}</strong> (<span class="tabular-nums">{{ number_format($calcGlobalCorrienteMeses) }}</span> m)
                                    </span>
                                    <span class="inline-flex items-center gap-1 whitespace-nowrap">
                                        <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                                        Con adeudo: <strong class="tabular-nums">{{ number_format($calcGlobalAdeudoCount) }}</strong> (<span class="tabular-nums">{{ number_format($calcGlobalAdeudoMeses) }}</span> m)
                                    </span>
                                </div>
                            </div>

                            <!-- Tarjetas globales: Adeudos y Al Corriente -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-2.5">
                                <div class="border border-red-200 bg-red-50/40 rounded-lg subtarjeta-bloque sm:p-3 flex flex-col justify-between">
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <span class="font-bold text-red-800 uppercase titulo-tarjeta">Adeudos Globales</span>
                                        <span class="px-1.5 py-0.5 rounded-full bg-red-100 text-red-800 font-bold detalle-fluido tabular-nums shrink-0">
                                            {{ number_format($calcGlobalAdeudoMeses) }} {{ $calcGlobalAdeudoMeses == 1 ? 'mes' : 'meses' }}
                                        </span>
                                    </div>
                                    <div class="cifra-principal font-extrabold text-red-600 leading-tight tabular-nums whitespace-nowrap">
                                        {{ number_format($calcGlobalAdeudoCount) }} <span class="detalle-fluido font-semibold text-gray-600">usuarios</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-red-100 rounded-full overflow-hidden my-1.5">
                                        <div class="bg-red-500 h-full rounded-full" style="width: {{ $pctGlobalAdeudo }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between gap-1 detalle-fluido pt-1 border-t border-red-100">
                                        <span class="text-gray-600 shrink-0">Adeudo:</span>
                                        <span class="monto-destacado font-extrabold text-red-700 tabular-nums whitespace-nowrap text-right">${{ number_format($calcGlobalAdeudoMonto, 2) }}</span>
                                    </div>
                                </div>

                                <div class="border border-indigo-200 bg-indigo-50/40 rounded-lg subtarjeta-bloque sm:p-3 flex flex-col justify-between">
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <span class="font-bold text-indigo-800 uppercase titulo-tarjeta">Al Corriente</span>
                                        <span class="px-1.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 font-bold detalle-fluido tabular-nums shrink-0">
                                            {{ number_format($calcGlobalCorrienteMeses) }} {{ $calcGlobalCorrienteMeses == 1 ? 'mes' : 'meses' }}
                                        </span>
                                    </div>
                                    <div class="cifra-principal font-extrabold text-indigo-600 leading-tight tabular-nums whitespace-nowrap">
                                        {{ number_format($calcGlobalCorrienteCount) }} <span class="detalle-fluido font-semibold text-gray-600">usuarios</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-indigo-100 rounded-full overflow-hidden my-1.5">
                                        <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $pctGlobalCorriente }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between gap-1 detalle-fluido pt-1 border-t border-indigo-100">
                                        <span class="text-gray-600 shrink-0">Al día:</span>
                                        <span class="monto-destacado font-extrabold text-indigo-700 tabular-nums whitespace-nowrap text-right">${{ number_format($calcGlobalCorrienteMonto, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-1.5 border-t border-gray-100 flex flex-wrap items-center justify-between gap-1 detalle-fluido text-gray-500">
                            <span>Meses adeudados: <strong class="tabular-nums">{{ number_format($calcGlobalAdeudoMeses) }}</strong></span>
                            <span>Meses pagados: <strong class="tabular-nums">{{ number_format($calcGlobalCorrienteMeses) }}</strong></span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>