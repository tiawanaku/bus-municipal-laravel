<div>
    <div>
        <div class="flex space-x-4">
            <div class="flex-1">
                <label for="start_date" class="block text-sm font-medium text-gray-300">Fecha de inicio:</label>
                <input type="date" wire:model="startDay"
                    class="filament-input form-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div class="flex-1">
                <label for="end_date" class="block text-sm font-medium text-gray-300">Fecha final:</label>
                <input type="date" wire:model="endDay"
                    class="filament-input form-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
        </div>
        <div class="mt-6">
            <x-filament::button wire:click="fetchMapData" class="">
                Generar Mapa
            </x-filament::button>
        </div>
        

        @if ($mapUrl)
        <div class="mt-6">
            <iframe src="{{ $mapUrl }}" width="100%" height="500px" frameborder="0"></iframe>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger mt-2">
                {{ session('error') }}
            </div>
        @endif

    </div>