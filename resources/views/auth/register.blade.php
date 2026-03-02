<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-unah-gradient bg-[length:400%_400%] animate-gradient-move p-6">
        <div class="w-[400px] px-6 py-12 bg-white shadow-xl rounded-2xl mt-16">
            <!-- Logos -->
            <div class="flex justify-center items-center gap-6 mb-6">
                <img src="{{ asset('img/UNAH-version-horizontal.png') }}" alt="UNAH Logo" class="h-16">
                <img src="{{ asset('img/Informatica-Administrativa.png') }}" alt="IA Logo" class="h-16">
            </div>

            <h2 class="text-center text-2xl font-bold text-unahblue">
                Registro para Práctica Profesional Supervisada
            </h2>
            <p class="text-center text-sm text-gray-600 mt-1 mb-6">
                Completa el formulario para registrarte
            </p>

            @if ($errors->any())
                <div class="mb-4">
                    <div class="font-medium text-red-600">¡Ups! Hubo algunos problemas con tu registro.</div>
                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nombre -->
                <div>
                    <x-label for="name" value="Nombre Completo" />
                    <x-input id="name" class="block mt-1 w-full uppercase" type="text" name="name" required autofocus 
                        oninput="this.value = this.value.replace(/[^a-zA-ZÁÉÍÓÚÑáéíóúñ\s]/g, '').toUpperCase()" />
                </div>

                <!-- Correo -->
                <div class="mt-4">
                    <x-label for="email" value="Correo Institucional" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" required />
                </div>

                <!-- Contraseña -->
                <div class="mt-4">
                    <x-label for="password" value="Contraseña" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                </div>

                <!-- Confirmar contraseña -->
                <div class="mt-4">
                    <x-label for="password_confirmation" value="Confirmar Contraseña" />
                    <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a class="underline text-sm text-gray-600 hover:text-unahblue" href="{{ route('login') }}">
                        ¿Ya tienes una cuenta? Inicia sesión aquí
                    </a>

                    <button type="submit" class="ml-3 px-4 py-2 bg-unahblue text-white rounded hover:bg-unahgold transition">
                        REGISTRARSE
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
