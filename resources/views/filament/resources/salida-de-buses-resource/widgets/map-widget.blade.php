<x-filament-widgets::widget >
    <x-filament::section>

    <div>
    {{-- Contenedor del iframe mapa seguimiento --}}
    <div 
        class="h-[700px] overflow-hidden border border-gray-300 rounded"
    >
        <iframe 
            src="{{ $iframeUrl }}" 
            style="border: 0; width: 100%; height: 600px;" 
            loading="lazy">
        </iframe>
    </div>
</div>


    </x-filament::section>
</x-filament-widgets::widget>
