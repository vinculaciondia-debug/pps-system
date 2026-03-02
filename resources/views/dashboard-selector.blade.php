<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Rol - Sistema PPS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ====== ANIMACIÓN DE FONDO TIPO LOGIN ====== */
        @keyframes diagonalGradient {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0% 0%; }
        }

        .bg-pps-animated {
            background-image: linear-gradient(
                135deg,
                #001f3f,
                #003366,
                #0056b3,
                #ffcc00
            );
            background-size: 300% 300%;
            animation: diagonalGradient 22s ease-in-out infinite;
        }

        /* Animación de entrada */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .delay-100 { animation-delay: 0.1s; opacity: 0; }
        .delay-200 { animation-delay: 0.2s; opacity: 0; }
        .delay-300 { animation-delay: 0.3s; opacity: 0; }

        .role-card {
            display: flex;
            flex-direction: column;
            min-height: 320px;
            height: 100%;
        }

        .role-card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>
</head>

<body class="min-h-screen bg-pps-animated relative overflow-hidden">

    <!-- Círculos decorativos -->
    <div class="pointer-events-none absolute -top-24 -left-20 w-72 h-72 bg-white/15 rounded-full blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -right-28 w-80 h-80 bg-yellow-300/25 rounded-full blur-3xl"></div>

    <!-- CONTENEDOR PRINCIPAL CENTRADO -->
<div class="relative z-10 max-w-6xl mx-auto px-4 pt-12 pb-12 flex flex-col items-center">

        <!-- HEADER -->
      
            <h1 class="text-3xl sm:text-4xl font-bold text-white drop-shadow mb-2">
                Selecciona un Rol 
            </h1>

            <p class="text-base sm:text-lg text-slate-100/90">
                Elige cómo deseas ingresar al sistema en esta sesión
            </p>
            <p class="text-xs sm:text-sm text-slate-100/80 mt-1">
                Bienvenido, <strong class="text-yellow-300">{{ auth()->user()->name }}</strong>
            </p>
        </div>

        <!-- GRID DE ROLES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10 auto-rows-fr w-full">

            @php $delay = 100; @endphp

            @if(in_array('admin', $roles))
            <!-- CARD ADMIN -->
           <a href="{{ route('admin.dashboard', ['force' => 1]) }}"
               class="group relative bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden border-2 border-transparent hover:border-blue-400 animate-fade-in-up delay-{{ $delay }} role-card">

                @php $delay += 100; @endphp

                <div class="absolute top-0 right-0 w-28 h-28 bg-blue-200/40 rounded-full blur-3xl"></div>

                <div class="relative p-8 role-card-content">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-all">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2">Administrador</h2>
                        <p class="text-slate-600 text-sm sm:text-base mb-5">
                            Gestionar usuarios, solicitudes y supervisores del sistema
                        </p>
                    </div>

                    <div class="text-blue-600 font-semibold flex items-center justify-center gap-2 group-hover:gap-3 transition-all">
                        <span>Ingresar</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                </div>
            </a>
            @endif

            @if(in_array('vinculacion', $roles))
            <!-- CARD VINCULACIÓN -->
            <a href="{{ route('admin.dashboard') }}"
               class="group relative bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden border-2 border-transparent hover:border-purple-400 animate-fade-in-up delay-{{ $delay }} role-card">

                @php $delay += 100; @endphp

                <div class="absolute top-0 right-0 w-28 h-28 bg-purple-200/40 rounded-full blur-3xl"></div>

                <div class="relative p-8 role-card-content">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-all">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2">Vinculación</h2>
                        <p class="text-slate-600 text-sm sm:text-base mb-5">
                            Gestionar vinculación y prácticas profesionales
                        </p>
                    </div>

                    <div class="text-purple-600 font-semibold flex items-center justify-center gap-2 group-hover:gap-3 transition-all">
                        <span>Ingresar</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                </div>
            </a>
            @endif

            @if(in_array('supervisor', $roles))
            <!-- CARD SUPERVISOR -->
            <a href="{{ route('supervisor.dashboard') }}"
               class="group relative bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden border-2 border-transparent hover:border-green-400 animate-fade-in-up delay-{{ $delay }} role-card">

                <div class="absolute top-0 right-0 w-28 h-28 bg-green-200/40 rounded-full blur-3xl"></div>

                <div class="relative p-8 role-card-content">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-all">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2">Supervisor</h2>
                        <p class="text-slate-600 text-sm sm:text-base mb-5">
                            Ver alumnos asignados y gestionar supervisiones
                        </p>
                    </div>

                    <div class="text-green-600 font-semibold flex items-center justify-center gap-2 group-hover:gap-3 transition-all">
                        <span>Ingresar</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                </div>
            </a>
            @endif

        </div>

        <!-- BOTÓN LOGOUT -->
        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white/15 backdrop-blur-sm text-white rounded-xl hover:bg-white/25 transition font-medium shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>

    </div>

</body>
</html>
