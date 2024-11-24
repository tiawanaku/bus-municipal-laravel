<div>
    <div>
    <label for="start_date">Fecha de inicio:</label>
<input type="date" wire:model="start_day" class="form-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

    </div>
    <div>
    <label for="start_date">Fecha final:</label>

    <input type="date" wire:model="end_day" class="form-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

    </div>
    <button wire:click="fetchMapData" >Generar Mapa</button>

    @if ($mapUrl)
        <iframe src="{{ $mapUrl }}" width="100%" height="500px" frameborder="0"></iframe>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger mt-2">
            {{ session('error') }}
        </div>
    @endif
   
</div>