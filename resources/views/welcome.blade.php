<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - PPS UNAH</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col justify-center items-center bg-unah-gradient bg-[length:400%_400%] animate-gradient-move p-6">
<div class="bg-white rounded-2xl shadow-lg p-10 max-w-xl w-full text-center animate-fade-in-down">
        
        <!-- Logos -->
        <div class="flex justify-center items-center gap-6 mb-6">
            <img src="{{ asset('img/UNAH-version-horizontal.png') }}" alt="UNAH Logo" class="h-16">
            <img src="{{ asset('img/Informatica-Administrativa.png') }}" alt="IA Logo" class="h-16">
        </div>

        <!-- Título -->
        <h1 class="text-2xl font-bold text-unahblue mb-2">Práctica Profesional Supervisada</h1>
        <p class="text-gray-700 mb-6">Bienvenido al sistema en línea para la gestión de tu práctica profesional. <br> Usa las opciones abajo para ingresar o registrarte.</p>

        <!-- Botones -->
        <div class="flex justify-center gap-4">
            <a href="{{ route('login') }}" class="bg-unahblue text-white px-6 py-2 rounded-md hover:bg-blue-800 transition">Iniciar Sesión</a>
         
        </div>
    </div>
</body>
</html>
