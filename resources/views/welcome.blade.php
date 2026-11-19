<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Sistema Agua') }} - Control de Agua Potable</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            overflow-x: hidden;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            overflow-x: hidden;
        }

        /* TARJETAS DE MODULOS */
        .item-modulo-teal {
            transition: transform 0.2s ease,
                background-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .item-modulo-teal:hover {
            transform: translateY(-3px);
        }

        @media (min-width: 768px) {
            .item-modulo-teal:not(:last-child) {
                border-right: 1px solid #0d9488;
            }
        }

        /* MOVILES */
        @media (max-width: 639px) {

            .titulo-principal {
                font-size: clamp(1.45rem, 6.8vw, 1.9rem);
                line-height: 1.2;
            }

            .gota-principal {
                width: 2.75rem;
                height: 3.3rem;
            }

            .item-modulo-teal {
                padding: 1rem 0.875rem;
            }

            .item-modulo-teal svg {
                width: 2rem;
                height: 2rem;
                margin-bottom: 0.45rem;
                stroke-width: 1.85;
            }

            .modulos-container {
                padding: 0.5rem;
            }
        }

        /* TABLETS */
        @media (min-width: 640px) and (max-width: 767px) {

            .titulo-principal {
                font-size: 2.25rem;
            }
        }
    </style>
</head>

<body
    class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col selection:bg-teal-600 selection:text-white">

    <!-- BARRA SUPERIOR -->
    <header class="w-full bg-white border-b border-slate-200 sticky top-0 z-30">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 min-h-16 py-2 flex items-center justify-between gap-3">

            <!-- LOGOTIPO -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 min-w-0">

                <div
                    class="w-9 h-9 flex-shrink-0 rounded-lg bg-gradient-to-br from-teal-500 to-teal-700 flex items-center justify-center text-white shadow-sm">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3.25C12 3.25 5.5 10.1 5.5 14.5a6.5 6.5 0 1 0 13 0c0-4.4-6.5-11.25-6.5-11.25Z" />
                    </svg>

                </div>

                <div class="leading-tight min-w-0">
                    <span class="font-extrabold text-sm sm:text-base text-slate-900 tracking-tight block">
                        BOQUILLAS
                    </span>

                    <span class="text-[10px] sm:text-[11px] font-medium text-teal-700 block">
                        Comité de Agua Potable
                    </span>
                </div>

            </a>

            <!-- ACCESO -->
            @if (Route::has('login'))

                <nav class="flex items-center flex-shrink-0 gap-2">

                    @auth

                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs sm:text-sm font-semibold transition">

                            <span>Ir al Panel</span>

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>

                        </a>

                    @else

                        <a href="{{ route('login') }}"
                            class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs sm:text-sm font-semibold transition">

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3" />
                            </svg>

                            <span>Iniciar Sesión</span>

                        </a>

                    @endauth

                </nav>

            @endif

        </div>

    </header>


    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-1 w-full py-6 sm:py-10 md:py-12 px-4 sm:px-6 space-y-6 sm:space-y-10">

        <!-- ENCABEZADO -->
        <section class="max-w-4xl mx-auto text-center space-y-2.5 sm:space-y-3">

            <!-- TITULO -->
            <h1
                class="titulo-principal text-2xl sm:text-3xl md:text-4xl font-extrabold text-teal-700 tracking-tight px-1">

                Control y Gestión de Agua Potable

            </h1>


            <!-- GOTA DE AGUA -->
            <div class="flex justify-center py-0.5 sm:py-2">

                <svg class="gota-principal w-14 h-16 sm:w-16 sm:h-20 md:w-20 md:h-24 drop-shadow-md"
                    viewBox="0 0 100 120" xmlns="http://www.w3.org/2000/svg">

                    <defs>
                        <linearGradient id="gotaAgua" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#5EEAD4" />
                            <stop offset="50%" stop-color="#0F766E" />
                            <stop offset="100%" stop-color="#115E59" />
                        </linearGradient>
                    </defs>

                    <path d="M50 5 C50 5 10 55 10 78
                        C10 101 27 115 50 115
                        C73 115 90 101 90 78
                        C90 55 50 5 50 5Z" fill="url(#gotaAgua)" />

                    <path d="M32 75 C32 62 43 43 48 36" fill="none" stroke="white" stroke-width="5"
                        stroke-linecap="round" opacity="0.7" />

                    <ellipse cx="35" cy="85" rx="5" ry="8" fill="white" opacity="0.35" />

                </svg>

            </div>
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-xs font-semibold">

                <span class="w-2 h-2 rounded-full bg-teal-600"></span>

                Ej. Boquilla de las Perlas

            </span>
            <p class="text-xs sm:text-sm md:text-base text-slate-600 max-w-2xl mx-auto px-2">

                Operado por el comité de agua potable de la comunidad.

            </p>

        </section>


        <!-- MODULOS DEL SISTEMA -->
        <section
            class="max-w-6xl mx-auto w-full bg-white border border-slate-200 rounded-2xl py-2.5 sm:py-5 px-3 sm:px-5 md:px-6 shadow-sm">

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 items-stretch text-center">

                <!-- 1. CAJA Y MATRIZ DE PAGOS -->
                <a href="{{ \Illuminate\Support\Facades\Route::has('pagos.index') ? route('pagos.index') : (\Illuminate\Support\Facades\Route::has('login') ? route('login') : '#') }}"
                    class="item-modulo-teal flex flex-col items-center justify-center px-4 py-5 sm:py-6 text-slate-800 hover:text-teal-700 hover:bg-teal-50/50 rounded-xl md:rounded-none group">

                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-teal-700 group-hover:text-teal-800 mb-2.5 sm:mb-3"
                        fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">

                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 7.5v9" />
                        <path
                            d="M14.3 9.7c0-1.1-1-1.8-2.3-1.8s-2.3.7-2.3 1.8c0 2 4.6 1.4 4.6 3.6 0 1.1-1 1.9-2.3 1.9s-2.3-.8-2.3-1.9" />

                    </svg>

                    <span class="text-sm font-bold text-slate-900 group-hover:text-teal-700 leading-snug">
                        Pagos
                    </span>

                    <span class="text-xs text-slate-500 mt-1 leading-relaxed max-w-[14rem]">
                        Cobro mensual, control de adeudos y recibos con folio QR.
                    </span>

                </a>


                <!-- 2. PADRON Y TOMAS -->
                <a href="{{ \Illuminate\Support\Facades\Route::has('usuarios.index') ? route('usuarios.index') : (\Illuminate\Support\Facades\Route::has('login') ? route('login') : '#') }}"
                    class="item-modulo-teal flex flex-col items-center justify-center px-4 py-5 sm:py-6 text-slate-800 hover:text-teal-700 hover:bg-teal-50/50 rounded-xl md:rounded-none group">

                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-teal-700 group-hover:text-teal-800 mb-2.5 sm:mb-3"
                        fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">

                        <path d="M16 21v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1" />
                        <circle cx="9" cy="8" r="3.5" />
                        <path d="M22 21v-1a3.5 3.5 0 0 0-2.8-3.4" />
                        <path d="M16 4.7a3.5 3.5 0 0 1 0 6.6" />

                    </svg>

                    <span class="text-sm font-bold text-slate-900 group-hover:text-teal-700 leading-snug">
                        Padrón y Tomas
                    </span>

                    <span class="text-xs text-slate-500 mt-1 leading-relaxed max-w-[14rem]">
                        Registro de usuarios, tarifas y estatus.
                    </span>

                </a>


                <!-- 3. REDES DE TUBERIA -->
                <a href="{{ \Illuminate\Support\Facades\Route::has('redes.index') ? route('redes.index') : (\Illuminate\Support\Facades\Route::has('login') ? route('login') : '#') }}"
                    class="item-modulo-teal flex flex-col items-center justify-center px-4 py-5 sm:py-6 text-slate-800 hover:text-teal-700 hover:bg-teal-50/50 rounded-xl md:rounded-none group">

                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-teal-700 group-hover:text-teal-800 mb-2.5 sm:mb-3"
                        fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">

                        <rect x="3" y="9" width="3.5" height="6" rx="0.8" />
                        <rect x="17.5" y="9" width="3.5" height="6" rx="0.8" />
                        <path d="M6.5 10.5h11" />
                        <path d="M6.5 13.5h11" />
                        <path d="M12 13.5v5" />
                        <path d="M9.5 18.5h5" />

                    </svg>

                    <span class="text-sm font-bold text-slate-900 group-hover:text-teal-700 leading-snug">
                        Redes de Tubería
                    </span>

                    <span class="text-xs text-slate-500 mt-1 leading-relaxed max-w-[14rem]">
                        Mapeo de líneas de distribución y tomas de usuarios.
                    </span>

                </a>


                <!-- 4. HORARIOS DE SUMINISTRO -->
                <a href="{{ \Illuminate\Support\Facades\Route::has('horarios.index') ? route('horarios.index') : (\Illuminate\Support\Facades\Route::has('login') ? route('login') : '#') }}"
                    class="item-modulo-teal flex flex-col items-center justify-center px-4 py-5 sm:py-6 text-slate-800 hover:text-teal-700 hover:bg-teal-50/50 rounded-xl md:rounded-none group">

                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-teal-700 group-hover:text-teal-800 mb-2.5 sm:mb-3"
                        fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">

                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 7.5v4.5l3 2" />

                    </svg>

                    <span class="text-sm font-bold text-slate-900 group-hover:text-teal-700 leading-snug">
                        Horarios de Suministro
                    </span>

                    <span class="text-xs text-slate-500 mt-1 leading-relaxed max-w-[14rem]">
                        Programación de tandeo y distribución por redes.
                    </span>

                </a>

            </div>

        </section>

    </main>


    <!-- PIE DE PAGINA -->
    <footer class="border-t border-slate-200 bg-white py-4 px-4 sm:px-6 mt-auto">

        <div
            class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] sm:text-xs text-slate-500 text-center sm:text-left">

            <span>
                &copy; {{ date('Y') }}
                <strong>Boquillas</strong> — Comité de Agua Potable.
            </span>

            <span>
                Desarrollado por <strong>AJL Solutions</strong>
            </span>

        </div>

    </footer>

</body>

</html>