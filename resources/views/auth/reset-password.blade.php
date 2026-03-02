<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-unah-gradient bg-[length:400%_400%] animate-gradient-move p-6">
        <div class="w-full max-w-md px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-lg mt-10">
            
            <!-- Logos -->
            <div class="flex justify-center items-center gap-6 mb-6">
                <img src="{{ asset('img/UNAH-version-horizontal.png') }}" alt="UNAH Logo" class="h-16">
                <img src="{{ asset('img/Informatica-Administrativa.png') }}" alt="IA Logo" class="h-16">
            </div>

            <h2 class="text-center text-2xl font-bold text-unahblue">
                Restablecer contraseña
            </h2>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="mt-4">
                    <x-label for="email" value="Correo institucional" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-label for="password" value="Nueva contraseña" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-label for="password_confirmation" value="Confirmar nueva contraseña" />
                    <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-unahblue underline">
                        ← Volver al login
                    </a>

                    <button class="px-4 py-2 bg-unahblue text-white rounded hover:bg-unahgold transition">
                        Restablecer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

