<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vinculación - Práctica Profesional</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
        .nav-link-active { background-color: #FFD700; color: #003f87; }
    </style>
</head>
<body class="bg-slate-100">
<div class="min-h-screen flex flex-col">

    <nav class="bg-unahblue shadow-lg sticky top-0 z-50"
         x-data="{ mobileOpen: false, solOpen: false, gestionOpen: false, sistemaOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Logo --}}
                <div class="flex items-center gap-3 flex-shrink-0">
                    <img src="{{ asset('img/UNAH-escudo.png') }}" alt="Logo UNAH" class="h-10 w-auto">
                    <div class="h-8 w-px bg-yellow-400"></div>
                    <div>
                        <h1 class="text-white font-bold text-sm leading-tight">Área de Vinculación UNAH</h1>
                        <p class="text-yellow-300 text-xs">Práctica Profesional</p>
                    </div>
                </div>

                {{-- Links Desktop --}}
                <div class="hidden lg:flex items-center gap-1">

                    {{-- Dashboard --}}
                    <a href="{{ route('admin.dashboard') }}"
                       class="px-3 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-1.5 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>

                    {{-- Solicitudes dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape.window="open = false"
                                class="px-3 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-1.5 text-sm font-medium {{ request()->routeIs('admin.solicitudes*') ? 'nav-link-active' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Solicitudes
                            <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-44 bg-white rounded-lg shadow-xl ring-1 ring-yellow-300/60 p-1 z-50">
                            <a href="{{ route('admin.solicitudes.pendientes') }}" @click="open=false"
                               class="block px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.solicitudes.pendientes') ? 'bg-yellow-100 text-unahblue font-semibold' : '' }}">Pendientes</a>
                            <a href="{{ route('admin.solicitudes.aprobadas') }}" @click="open=false"
                               class="block px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.solicitudes.aprobadas') ? 'bg-yellow-100 text-unahblue font-semibold' : '' }}">Aprobadas</a>
                            <a href="{{ route('admin.solicitudes.rechazadas') }}" @click="open=false"
                               class="block px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.solicitudes.rechazadas') ? 'bg-yellow-100 text-unahblue font-semibold' : '' }}">Rechazadas</a>
                            <a href="{{ route('admin.solicitudes.actualizacion') }}" @click="open=false"
                               class="block px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.solicitudes.actualizacion') ? 'bg-yellow-100 text-unahblue font-semibold' : '' }}">Actualización</a>
                            <a href="{{ route('admin.solicitudes.finalizadas') }}" @click="open=false"
                               class="block px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.solicitudes.finalizadas') ? 'bg-yellow-100 text-unahblue font-semibold' : '' }}">Finalizadas</a>
                        </div>
                    </div>

                    {{-- Gestión dropdown (Supervisores, Empresas, Usuarios) --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape.window="open = false"
                                class="px-3 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-1.5 text-sm font-medium {{ request()->routeIs('admin.supervisores*', 'admin.empresas*', 'admin.usuarios*') ? 'nav-link-active' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Gestión
                            <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-44 bg-white rounded-lg shadow-xl ring-1 ring-yellow-300/60 p-1 z-50">
                            <a href="{{ route('admin.supervisores.index') }}" @click="open=false"
                               class="flex items-center gap-2 px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.supervisores*') ? 'bg-yellow-100 text-unahblue font-semibold' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Supervisores
                            </a>
                            <a href="{{ route('admin.empresas.index') }}" @click="open=false"
                               class="flex items-center gap-2 px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.empresas*') ? 'bg-yellow-100 text-unahblue font-semibold' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Empresas
                            </a>
                            <a href="{{ route('admin.usuarios.index') }}" @click="open=false"
                               class="flex items-center gap-2 px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.usuarios*') ? 'bg-yellow-100 text-unahblue font-semibold' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                Usuarios
                            </a>
                        </div>
                    </div>

                    {{-- Reportes --}}
                    <a href="{{ route('admin.reportes') }}"
                       class="px-3 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-1.5 text-sm font-medium {{ request()->routeIs('admin.reportes') ? 'nav-link-active' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Reportes
                    </a>

                    {{-- Sistema dropdown (Formatos, Auditoría) --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape.window="open = false"
                                class="px-3 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-1.5 text-sm font-medium {{ request()->routeIs('admin.formatos*', 'admin.audit*') ? 'nav-link-active' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                            Sistema
                            <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-xl ring-1 ring-yellow-300/60 p-1 z-50">
                            <a href="{{ route('admin.formatos.index') }}" @click="open=false"
                               class="flex items-center gap-2 px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.formatos*') ? 'bg-yellow-100 text-unahblue font-semibold' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Formatos
                            </a>
                            <a href="{{ route('admin.audit.index') }}" @click="open=false"
                               class="flex items-center gap-2 px-3 py-2 text-sm rounded-md text-gray-700 hover:bg-yellow-100 hover:text-unahblue transition {{ request()->routeIs('admin.audit*') ? 'bg-yellow-100 text-unahblue font-semibold' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                Auditoría
                            </a>
                        </div>
                    </div>

                    {{-- Cerrar Sesión --}}
                    <form method="POST" action="{{ route('logout') }}" class="ml-1">
                        @csrf
                        <button type="submit"
                                class="px-3 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition flex items-center gap-1.5 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Cerrar Sesión
                        </button>
                    </form>

                </div>

                {{-- Botón hamburguesa móvil --}}
                <button @click="mobileOpen = !mobileOpen"
                        class="lg:hidden text-white p-2 rounded-lg hover:bg-yellow-400 hover:text-unahblue transition">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Menú Móvil --}}
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden bg-unahblue border-t border-yellow-400">
            <div class="px-4 py-3 space-y-1">

                <a href="{{ route('admin.dashboard') }}"
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-3 {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                {{-- Solicitudes móvil --}}
                <div>
                    <button @click="solOpen = !solOpen"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="font-medium">Solicitudes</span>
                        </span>
                        <svg class="w-4 h-4 transition-transform" :class="solOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="solOpen" x-cloak class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.solicitudes.pendientes') }}" class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">Pendientes</a>
                        <a href="{{ route('admin.solicitudes.aprobadas') }}" class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">Aprobadas</a>
                        <a href="{{ route('admin.solicitudes.rechazadas') }}" class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">Rechazadas</a>
                        <a href="{{ route('admin.solicitudes.actualizacion') }}" class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">Actualización</a>
                        <a href="{{ route('admin.solicitudes.finalizadas') }}" class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">Finalizadas</a>
                    </div>
                </div>

                {{-- Gestión móvil --}}
                <div>
                    <button @click="gestionOpen = !gestionOpen"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="font-medium">Gestión</span>
                        </span>
                        <svg class="w-4 h-4 transition-transform" :class="gestionOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="gestionOpen" x-cloak class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.supervisores.index') }}" class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">Supervisores</a>
                        <a href="{{ route('admin.empresas.index') }}" class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">Empresas</a>
                        <a href="{{ route('admin.usuarios.index') }}" class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">Usuarios</a>
                    </div>
                </div>

                <a href="{{ route('admin.reportes') }}"
                   class="block px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="font-medium">Reportes</span>
                </a>

                {{-- Sistema móvil --}}
                <div>
                    <button @click="sistemaOpen = !sistemaOpen"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            <span class="font-medium">Sistema</span>
                        </span>
                        <svg class="w-4 h-4 transition-transform" :class="sistemaOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="sistemaOpen" x-cloak class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('admin.formatos.index') }}" class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">Formatos</a>
                        <a href="{{ route('admin.audit.index') }}" class="block px-4 py-2 rounded-lg text-white hover:bg-yellow-400 hover:text-unahblue transition text-sm">Auditoría</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-yellow-400">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-3 rounded-lg bg-red-500 text-white hover:bg-red-600 transition flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="font-medium">Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <x-footer />
</div>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
