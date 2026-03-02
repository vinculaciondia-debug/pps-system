<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Estudiante UNAH - Práctica Profesional</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        .nav-link-active {
            background-color: #FFD700;
            color: #003f87;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
<div class="min-h-screen flex flex-col">

    <!-- Navbar Estudiantes -->
    <nav class="bg-unahblue shadow-lg sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo y Título -->
                <div class="flex items-center gap-3">
                    <!-- Logo sin fondo cuadrado -->
                    <img src="{{ asset('img/UNAH-escudo.png') }}" alt="Logo UNAH" class="h-12 w-auto flex-shrink-0">
                    
                    <!-- Separador vertical (opcional) -->
                    <div class="h-10 w-px bg-yellow-400"></div>
                    
                    <!-- Texto al lado del logo -->
                    <div>
                        <h1 class="text-white font-bold text-base leading-tight">Estudiante UNAH</h1>
                        <p class="text-yellow-300 text-xs">Práctica Profesional</p>
                    </div>
                </div>

                <!-- Links Desktop -->
                <div class="hidden lg:flex items-center space-x-1">
                    <a href="{{ route('estudiantes.dashboard') }}" 
                       class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('estudiantes.dashboard') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('estudiantes.solicitud') }}" 
                       class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('estudiantes.solicitud') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm font-medium">Solicitar</span>
                    </a>
                    
                    <a href="{{ route('estudiantes.cancelacion.create') }}" 
                       class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('estudiantes.cancelacion.*') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="text-sm font-medium">Cancelar</span>
                    </a>
                    
                    <a href="{{ route('estudiantes.actualizacion.create') }}" 
                       class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('estudiantes.actualizacion.*') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="text-sm font-medium">Actualizar</span>
                    </a>
                    
                    <a href="{{ route('estudiantes.formatos') }}" 
                       class="px-4 py-2.5 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-2 {{ request()->routeIs('estudiantes.formatos') ? 'nav-link-active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-medium">Formatos</span>
                    </a>
                </div>

                <!-- Logout Desktop -->
                <div class="hidden lg:flex items-center gap-2">
                    
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
                <a href="{{ route('estudiantes.dashboard') }}" 
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition {{ request()->routeIs('estudiantes.dashboard') ? 'nav-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </span>
                </a>
                
                <a href="{{ route('estudiantes.solicitud') }}" 
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition {{ request()->routeIs('estudiantes.solicitud') ? 'nav-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="font-medium">Solicitar Práctica</span>
                    </span>
                </a>
                
                <a href="{{ route('estudiantes.cancelacion.create') }}" 
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition {{ request()->routeIs('estudiantes.cancelacion.*') ? 'nav-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="font-medium">Cancelar Práctica</span>
                    </span>
                </a>
                
                <a href="{{ route('estudiantes.actualizacion.create') }}" 
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition {{ request()->routeIs('estudiantes.actualizacion.*') ? 'nav-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="font-medium">Actualizar Datos</span>
                    </span>
                </a>
                
                <a href="{{ route('estudiantes.formatos') }}" 
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition {{ request()->routeIs('estudiantes.formatos') ? 'nav-link-active' : '' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium">Formatos</span>
                    </span>
                </a>
                
                
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
</body>
</html>