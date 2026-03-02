<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-unah-gradient bg-[length:400%_400%] animate-gradient-move p-6">
        <div class="w-full max-w-md px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-lg mt-10">
            
            <div class="flex justify-center items-center gap-6 mb-6">
                <img src="{{ asset('img/UNAH-version-horizontal.png') }}" alt="UNAH Logo" class="h-16">
                <img src="{{ asset('img/Informatica-Administrativa.png') }}" alt="IA Logo" class="h-16">
            </div>

            <h2 class="text-center text-2xl font-bold text-unahblue">
                Verifica tu correo institucional
            </h2>
            <p class="text-center text-sm text-gray-600 mt-1 mb-6">
                Hemos enviado un enlace de verificación a tu correo. Haz clic en él para activar tu cuenta.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-green-600">
                    Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div class="flex items-center justify-between mt-6">
                    <a class="underline text-sm text-gray-600 hover:text-unahblue" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Cerrar sesión
                    </a>

                    <button type="submit" class="ml-3 px-4 py-2 bg-unahblue text-white rounded hover:bg-unahgold transition">
                        Reenviar correo
                    </button>
                </div>
            </form>

            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
            </form>
        </div>
    </div>
</x-guest-layout>
