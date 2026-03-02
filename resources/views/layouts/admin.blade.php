<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vinculación - Práctica Profesional</title>
    {{-- Tailwind vía Vite --}}
    @vite(['resources/css/app.css','resources/js/app.js'])

    {{-- Iconos (opcional) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
        .nav-link-active {
            background-color: #FFD700;
            color: #003f87;
        }
    </style>
</head>
<body class="bg-slate-100">
<div class="min-h-screen flex flex-col">

    <!-- Navbar Área de Vinculación -->
    <nav class="bg-unahblue shadow-lg sticky top-0 z-50" x-data="{ mobileMenuOpen: false, solicitudesOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo y Título -->
                <div class="flex items-center gap-3">
                    <!-- Logo sin fondo cuadrado -->
                    <img src="{{ asset('img/UNAH-escudo.png') }}" alt="Logo UNAH" class="h-12 w-auto flex-shrink-0">
                    
                    <!-- Separador vertical (opcional) -->
                    <div class=" h-10 w-px bg-yellow-400"></div>
                    
                    <!-- Texto al lado del logo -->
                    <div >
                        <h1 class="text-white font-bold text-lg leading-tight">Área de Vinculación UNAH</h1>
                        <p class="text-yellow-300 text-xs">Práctica Profesional</p>
                    </div>
                </div>
                <!-- Links Desktop -->
                <div class="hidden lg:flex items-center space-x-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>
                    
                    <!-- Solicitudes con dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            @keydown.escape.window="open = false"
                            :aria-expanded="open.toString()"
                            aria-haspopup="menu"
                            class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('admin.solicitudes*') ? 'nav-link-active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm font-medium">Solicitudes</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div
                            x-show="open"
                            x-cloak
                            @click.away="open = false"
                            @keydown.escape.window="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 mt-2 min-w-full w-max max-w-xs bg-white rounded-lg shadow-xl ring-1 ring-yellow-300/60 p-1 z-50 origin-top-left">
                            <a href="{{ route('admin.solicitudes.pendientes') }}"
                               @click="open=false"
                               class="block whitespace-nowrap px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.solicitudes.pendientes') ? 'bg-yellow-100 text-unahblue' : '' }}">
                                Pendientes
                            </a>
                            <a href="{{ route('admin.solicitudes.aprobadas') }}"
                               @click="open=false"
                               class="block whitespace-nowrap px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.solicitudes.aprobadas') ? 'bg-yellow-100 text-unahblue' : '' }}">
                                Aprobadas
                            </a>
                            <a href="{{ route('admin.solicitudes.rechazadas') }}"
                               @click="open=false"
                               class="block whitespace-nowrap px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.solicitudes.rechazadas') ? 'bg-yellow-100 text-unahblue' : '' }}">
                                Rechazadas
                            </a>
                            <a href="{{ route('admin.solicitudes.actualizacion') }}"
                               @click="open=false"
                               class="block whitespace-nowrap px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.solicitudes.actualizacion') ? 'bg-yellow-100 text-unahblue' : '' }}">
                                Actualización
                            </a>
                            <a href="{{ route('admin.solicitudes.finalizadas') }}"
                               @click="open=false"
                               class="block whitespace-nowrap px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.solicitudes.finalizadas') ? 'bg-yellow-100 text-unahblue' : '' }}">
                                Finalizadas
                            </a>
                        </div>
                    </div>
                    
                    <!-- Supervisores -->
                    <a href="{{ route('admin.supervisores.index') }}" 
                       class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('admin.supervisores*') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="text-sm font-medium">Supervisores</span>
                    </a>
                    
                    <!-- Reportes -->
                    <a href="{{ route('admin.reportes') }}" 
                       class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('admin.reportes') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm font-medium">Reportes</span>
                    </a>
                    
                    <!-- Formatos -->
                    <a href="{{ route('admin.formatos.index') }}" 
                       class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('admin.formatos') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-medium">Formatos</span>
                    </a>
                </div>

                <!-- Usuarios -->
<a href="{{ route('admin.usuarios.index') }}" 
   class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('admin.usuarios*') ? 'nav-link-active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    <span class="text-sm font-medium">Usuarios</span>
</a>

                <!-- Logout Desktop -->
                <div class="hidden lg:flex items-center">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2.5 rounded-lg bg-red-500 text-white hover:bg-red-600 transition flex items-center gap-2 text-sm font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>

                <!-- Botón Hamburguesa (Móvil) -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="lg:hidden text-white p-2 rounded-lg hover:bg-yellow-400 hover:text-unahblue transition">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>

        <!-- Menú Móvil -->
        <div x-show="mobileMenuOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="lg:hidden bg-unahblue border-t border-yellow-400">
            <div class="px-4 py-3 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </span>
                </a>
                
                <!-- Solicitudes con submenú móvil -->
                <div>
                    <button @click="solicitudesOpen = !solicitudesOpen"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="font-medium">Solicitudes</span>
                        </span>
                        <svg class="w-4 h-4" :class="{'rotate-180': solicitudesOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="solicitudesOpen" x-cloak class="ml-8 mt-2 space-y-1">
                        <a href="{{ route('admin.solicitudes.pendientes') }}" 
                           class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">
                            Pendientes
                        </a>
                        <a href="{{ route('admin.solicitudes.aprobadas') }}" 
                           class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">
                            Aprobadas
                        </a>
                        <a href="{{ route('admin.solicitudes.rechazadas') }}" 
                           class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">
                            Rechazadas
                        </a>
                        <a href="{{ route('admin.solicitudes.actualizacion') }}" 
                           class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">
                            Actualización
                        </a>
                        <a href="{{ route('admin.solicitudes.finalizadas') }}" 
                           class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">
                            Finalizadas
                        </a>
                    </div>
                </div>
                
                <!-- Supervisores -->
                <a href="{{ route('admin.supervisores.index') }}" 
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="font-medium">Supervisores</span>
                    </span>
                </a>
                
                <!-- Reportes -->
                <a href="{{ route('admin.reportes') }}" 
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="font-medium">Reportes</span>
                    </span>
                </a>
                
                <!-- Formatos -->
                <a href="{{ route('admin.formatos.index') }}" 
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium">Formatos</span>
                    </span>
                </a>
                
                <!-- Cerrar Sesión -->
                <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-yellow-400">
                    @csrf
                    <button type="submit" 
                            class="w-full text-left block px-4 py-3 rounded-lg bg-red-500 text-white hover:bg-red-600 transition">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="font-medium">Cerrar Sesión</span>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="flex-1">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
         @yield('content')
      </div>
   </main>

    <!-- Footer Component -->
    <x-footer />

</div>

{{-- Alpine para $dispatch en x-data (opcional si lo usas) --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

@stack('scripts')
</body>
</html>