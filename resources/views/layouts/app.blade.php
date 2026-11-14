<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        
        <!-- Contenedor general que fija la pantalla completa -->
        <div x-data="{ mobileMenuOpen: false }" class="h-screen flex overflow-hidden bg-gray-50">
            
            <!-- Menú de Navegación Lateral (Sidebar) -->
            @include('layouts.navigation')

            <!-- Contenedor Derecho (Barra Superior y Contenido) -->
            <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
                
                <!-- Barra Superior -->
                <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 z-20 shrink-0 gap-3">
                    
                    <!-- Izquierda: Botón Hamburguesa móvil + Título con escala progresiva -->
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-1.5 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none cursor-pointer shrink-0">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Título adaptativo: Se reduce gradualmente antes de llegar a recortarse -->
                        @isset($header)
                            <div class="font-bold text-[11px] sm:text-xs md:text-sm lg:text-base text-gray-800 tracking-tight truncate min-w-0" title="{{ strip_tags($header) }}">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                    <!-- Derecha: Menú de Usuario (Perfil y Cerrar Sesión) -->
                    <div class="flex items-center relative shrink-0" x-data="{ userDropdown: false }">
                        <button @click="userDropdown = !userDropdown" class="flex items-center gap-1.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 px-2.5 py-1.5 rounded-md focus:outline-none transition cursor-pointer">
                            <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">
                                {{ substr(Auth::user()->name ?? 'O', 0, 1) }}
                            </div>
                            <span class="text-xs font-semibold text-gray-700 hidden sm:inline">{{ Auth::user()->name ?? 'Operador' }}</span>
                            <svg class="h-3.5 w-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Dropdown de Usuario -->
                        <div x-show="userDropdown" @click.away="userDropdown = false" 
                             class="absolute right-0 top-11 w-56 bg-white border border-gray-200 rounded-lg shadow-xl py-1.5 z-50 text-xs">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <div class="text-gray-400 text-[10px]">Conectado como</div>
                                <div class="font-bold text-gray-800 truncate">{{ Auth::user()->name ?? 'Operador' }}</div>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                    ⚙️ Editar Perfil
                                </a>
                            </div>
                            <div class="border-t border-gray-100 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-red-600 hover:bg-red-50 font-semibold transition cursor-pointer">
                                        🚪 Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Área Desplazable del Contenido Principal -->
                <main class="flex-1 overflow-y-auto p-3 sm:p-5">
                    {{ $slot }}
                </main>

            </div>
        </div>
    </body>
</html>