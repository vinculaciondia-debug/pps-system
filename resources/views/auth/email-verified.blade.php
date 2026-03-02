<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-unah-gradient bg-[length:400%_400%] animate-gradient-move p-6">
        <div class="w-full max-w-md px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-lg mt-10">
            
            <div class="flex justify-center items-center gap-6 mb-6">
                <img src="{{ asset('img/UNAH-version-horizontal.png') }}" alt="UNAH Logo" class="h-16">
                <img src="{{ asset('img/Informatica-Administrativa.png') }}" alt="IA Logo" class="h-16">
            </div>

            <h2 class="text-center text-2xl font-bold text-unahblue">
                ¡Correo verificado correctamente!
            </h2>
            <p class="text-center text-sm text-gray-600 mt-1 mb-6">
                Gracias por verificar tu cuenta. Ya puedes acceder al sistema.
            </p>

            <div class="flex justify-center">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-unahblue text-white rounded hover:bg-unahgold transition">
                    Ir al inicio
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
