<x-filament-panels::page >
    @livewire(\App\Filament\Widgets\ResumenOperaciones::class)
    <div class="h-[250px] overflow-hidden [&_.fi-widget]:!h-[250px] [&_.fi-widget]:!min-h-0">
        @livewire(\App\Filament\Widgets\SalidaBusesChart::class)
    </div>

    @livewire(\App\Filament\Widgets\ResumenKilometrajeBusesTable::class)
</x-filament-panels::page>