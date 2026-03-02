@props(['name' => '', 'label' => ''])

<div>
    {{-- Etiqueta del input --}}
    <label class="block text-sm font-medium text-gray-700">
        {{ $label }}
    </label>

    {{-- Input de tipo archivo --}}
    <input 
        type="file"
        name="{{ $name }}"
        accept="application/pdf"
        required
        class="block w-full mt-1 text-sm border rounded-lg p-2 bg-gray-50 focus:ring focus:ring-unahblue/40"
        onchange="mostrarNombreArchivo(this)">
    
    {{-- Nombre del archivo seleccionado --}}
    <p class="text-xs text-gray-500 mt-1 nombre-archivo"></p>
</div>

<script>
function mostrarNombreArchivo(input) {
    const p = input.closest('div').querySelector('.nombre-archivo');
    if (input.files && input.files.length > 0) {
        p.textContent = 'Archivo seleccionado: ' + input.files[0].name;
    } else {
        p.textContent = '';
    }
}
</script>
