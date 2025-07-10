<x-filament-panels::page>
<x-filament-panels::page class="max-w-full">
    <div x-data="{ tab: 'estadisticas' }" class="space-y-4">

        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-4">
                <button x-on:click="tab = 'estadisticas'" :class="tab === 'estadisticas' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'"
                    class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                    Estadísticas Generales
                </button>
                <button x-on:click="tab = 'tecnicos'" :class="tab === 'tecnicos' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'"
                    class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                    Desempeño de Anfitriones
                </button>
                
            </nav>
        </div>

        <div x-show="tab === 'estadisticas'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
            

            <!-- Widgets en dos columnas -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                @livewire(\App\Filament\Widgets\TendenciaMantenimientosChart::class)
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                @livewire(\App\Filament\Widgets\TiposMantenimientosPastel::class)
            </div>
        </div>


        <div x-show="tab === 'tecnicos'" x-cloak>
            @livewire(\App\Filament\Widgets\RendimientoTecnicosTable::class)
        </div>

        <div x-show="tab === 'mantenimiento'" x-cloak>
            @livewire(\App\Filament\Widgets\ResumenMantenimientoBusesTable::class)
        </div>

    </div>
</x-filament-panels::page>
</x-filament-panels::page>
