<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}


        <div x-data="{ mostrarIframe: false }">
    {{-- Inputs de Fechas --}}
    <div class="flex flex-wrap items-end gap-4">
    <div>
        <label for="fechaInicio" class="block text-sm font-medium text-gray-700 dark:text-white">Fecha de Inicio</label>
        <input type="date" id="fechaInicio" wire:model="fechaInicio"
            class="filament-input form-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                   focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 
                   dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
    </div>

    <div>
        <label for="fechaFin" class="block text-sm font-medium text-gray-700 dark:text-white">Fecha de Fin</label>
        <input type="date" id="fechaFin" wire:model="fechaFin"
            class="filament-input form-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                   focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 
                   dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
    </div>

    <div class="mt-5">
        <button wire:click="generarReporte"
            class="px-4 py-2 text-sm bg-primary-600 text-white rounded hover:bg-primary-700 transition">
            Generar Reporte
        </button>
    </div>
</div>

    {{-- Mostrar iframe cuando haya URL --}}
    @if($iframeUrl)
        <div class="mt-4">
            <iframe 
                src="{{ $iframeUrl }}" 
                style="border: 0; width: 100%; height: 500px;" 
                loading="lazy">
            </iframe>
        </div>
    @endif
</div>

    </x-filament::section>
</x-filament-widgets::widget>
