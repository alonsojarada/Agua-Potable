<!-- Fondo oscuro semitransparente para móviles cuando el menú esté abierto -->
<div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="fixed inset-0 z-40 bg-black/50 md:hidden transition-opacity"></div>

<!-- Panel Lateral (Sidebar) -->
<aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0 shrink-0 h-full">
    
    <!-- Título / Logo Superior del Menú -->
    <div class="h-16 flex items-center px-6 border-b border-gray-100 shrink-0">
        <span class="font-bold text-sm text-blue-600 uppercase tracking-wide flex items-center gap-2">
            <span>💧</span> <span>Sistema Agua</span>
        </span>
    </div>

    <!-- Secciones de Navegación Verticales -->
    <div class="flex-1 py-4 space-y-1 px-3 overflow-y-auto text-xs">
        <div class="px-3 pb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Menú Principal</div>

        <a href="{{ route('pagos.matriz') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md font-semibold {{ request()->routeIs('pagos.matriz') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }} transition">
            <span class="text-base">📊</span>
            <span>Matriz General</span>
        </a>

        <a href="{{ route('pagos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md font-semibold {{ request()->routeIs('pagos.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }} transition">
            <span class="text-base">💰</span>
            <span>Caja Individual</span>
        </a>

        <a href="{{ route('redes.index') ?? '#' }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md font-semibold {{ request()->routeIs('redes.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }} transition">
            <span class="text-base">📡</span>
            <span>Redes</span>
        </a>

        <a href="{{ route('usuarios.index') ?? '#' }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md font-semibold {{ request()->routeIs('usuarios.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }} transition">
            <span class="text-base">👥</span>
            <span>Usuarios / Tomas</span>
        </a>

        <a href="{{ route('horarios.index') ?? '#' }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md font-semibold {{ request()->routeIs('horarios.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }} transition">
            <span class="text-base">🕒</span>
            <span>Horarios</span>
        </a>

        <a href="{{ route('map.index') ?? '#' }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md font-semibold {{ request()->routeIs('map.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }} transition">
            <span class="text-base">🗺️</span>
            <span>Mapa</span>
        </a>
    </div>

    <!-- Pie de Sidebar -->
    <div class="p-4 border-t border-gray-100 text-center shrink-0">
        <span class="text-[10px] text-gray-400">AJL Solutions</span>
    </div>
</aside>