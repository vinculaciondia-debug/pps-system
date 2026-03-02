<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-unah-gradient bg-[length:400%_400%] animate-gradient-move p-6">
        <div class="w-[430px] px-6 py-10 bg-white shadow-xl rounded-2xl mt-16">
            
            <!-- Logos -->
            <div class="flex justify-center items-center gap-6 mb-6">
                <img src="{{ asset('img/UNAH-version-horizontal.png') }}" alt="UNAH Logo" class="h-16">
                <img src="{{ asset('img/Informatica-Administrativa.png') }}" alt="IA Logo" class="h-16">
            </div>

            <h2 class="text-center text-2xl font-bold text-unahblue">
                ¿Olvidaste tu contraseña?
            </h2>
            <p class="text-center text-sm text-gray-600 mt-1 mb-6">
                Ingresa tu correo institucional para enviarte un enlace de recuperación.
            </p>

            <!-- Alerta de estado -->
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Formulario -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email -->
                <div>
                    <x-label for="email" value="Correo institucional" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="off" />
                </div>

                <!-- Acciones -->
                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('login') }}" class="text-sm text-unahblue font-semibold hover:underline">
                        ← Volver al login
                    </a>

                    <button type="submit" class="ml-3 px-4 py-2 bg-unahblue text-white rounded hover:bg-unahgold transition">
                        Enviar enlace
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
