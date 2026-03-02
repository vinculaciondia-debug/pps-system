<x-guest-layout>
<div class="min-h-screen flex flex-col justify-center items-center bg-unah-gradient bg-[length:400%_400%] animate-gradient-move p-6">
    <div class="w-[400px] px-6 py-12 bg-white shadow-xl rounded-2xl mt-16">


             <!-- Logos -->
        <div class="flex justify-center items-center gap-6 mb-6">
            <img src="{{ asset('img/UNAH-version-horizontal.png') }}" alt="UNAH Logo" class="h-16">
            <img src="{{ asset('img/Informatica-Administrativa.png') }}" alt="IA Logo" class="h-16">
        </div>

            <h2 class="text-center text-2xl font-bold text-unahblue">
                Inicio de sesión
            </h2>
            <p class="text-center text-sm text-gray-600 mt-1 mb-6">
                Accede con tu correo institucional
            </p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 rounded-xl border border-yellow-300 bg-yellow-50 px-4 py-4 text-sm text-yellow-800 shadow-sm">
                    <div class="flex items-start gap-2">
                        <div>
                            <p class="font-semibold">Acceso expirado</p>
                            <p class="mt-1">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-label for="email" value="Correo institucional" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-label for="password" value="Contraseña" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-unahblue shadow-sm focus:ring focus:ring-unahgold" name="remember">
                        <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-6">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-unahblue" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif

                    <button type="submit" class="ml-3 px-4 py-2 bg-unahblue text-white rounded hover:bg-unahgold transition">
    {{ __('INICIAR SESIÓN') }}
</button>

                </div>
   

            </form>
        </div>
    </div>

<script>
    setTimeout(() => {
        const alert = document.querySelector('[class*="bg-yellow-50"]');
        if (alert) alert.remove();
    }, 10000);
</script>

</x-guest-layout>
