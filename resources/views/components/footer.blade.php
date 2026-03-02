<!-- Footer Reutilizable - Práctica Profesional UNAH -->
<!-- Guardar en: resources/views/components/footer.blade.php -->

<footer class="footer-pattern text-white py-10 mt-auto relative">
   
    <img src="{{ asset('img/footer.jpg') }}" alt="" aria-hidden="true" style="display:none; width:1px; height:1px; object-fit:cover;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Contenido Principal del Footer -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            
            <!-- Columna Izquierda: Información del Departamento -->
            <div class="flex flex-col justify-center">
                <h3 class="text-xl font-bold mb-2 text-white uppercase">
                    Departamento de Informática Administrativa
                </h3>
                <p class="text-base text-gray-200 mb-4">Práctica Profesional Supervisada</p>
                
                <!-- Redes Sociales -->
                <div class="flex gap-3">
                    <a href="https://www.facebook.com/InformaticaAdmitivaUNAH?locale=es_LA" 
                       target="_blank"
                       class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-unahgold hover:text-unahblue transition-all duration-300 hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/infoadminunah?igsh=bTR6bmw1dmM0cHpp" 
                       target="_blank"
                       class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-unahgold hover:text-unahblue transition-all duration-300 hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Columna Derecha: Logos -->
            <div class="flex items-center justify-center md:justify-end gap-4 md:gap-2">
                <!-- Logo Informática Administrativa -->
                <div class="w-28 h-28 sm:w-36 sm:h-36 md:w-38 md:h-38 rounded-xl p-2 md:p-3">
                    <img src="{{ asset('img/Informatica-Administrativa.png') }}" 
                         alt="Informática Administrativa" 
                         class="w-full h-full object-contain">
                </div>
                
                <!-- Logo UNAH -->
                <div class="w-32 h-32 sm:w-36 sm:h-36 md:w-48 md:h-48 rounded-xl p-2 md:p-3">
                    <img src="{{ asset('img/logo-unah-blanco.png') }}" 
                         alt="Universidad Nacional Autónoma de Honduras" 
                         class="w-full h-full object-contain">
                </div>
            </div>
        </div>

        <!-- Separador -->
        <div class="border-t border-white/20 my-6"></div>

        <!-- Copyright -->
        <div class="text-center">
            <p class="text-sm text-gray-300 mb-1">
                © {{ date('Y') }} Universidad Nacional Autónoma de Honduras - Todos los derechos reservados
            </p>
            <p class="text-xs text-gray-400">
                Desarrollado por el Departamento de Informática Administrativa
            </p>
        </div>

    </div>

    <!-- Barra decorativa superior -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-unahgold via-yellow-500 to-unahgold"></div>
</footer>

<style>
    .footer-pattern {
        /* Fallback de color (evita fondo blanco mientras carga) */
        background-color: #0b3a57; /* color oscuro similar al diseño */
        min-height: 220px; /* evita salto si la imagen tarda */
        /* Imagen de fondo + leve capa oscura para contraste del texto */
        background:
            linear-gradient(rgba(0, 0, 0, 0.29), rgba(0, 0, 0, 0.12)),
            url('{{ asset("img/footer.jpg") }}') right center / cover no-repeat;
        position: relative;
        overflow: hidden;
        background-color: #0b3a57;
        /* evita repaint brusco */
        background-attachment: scroll;
    }
    /* Opcional: transición suave cuando la imagen aparece (muy sutil) */
    .footer-pattern {
        transition: background-color .15s linear;
    }

    .footer-pattern::before,
    .footer-pattern::after {
        content: none;
    }

    @media (max-width: 768px) {
        .footer-pattern {
            background-position: center;
        }
    }
</style>