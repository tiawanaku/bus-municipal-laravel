<?php

namespace App\Filament\Resources\BusResource\Pages;

use App\Filament\Resources\BusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use App\Models\Bus;


class CreateBus extends CreateRecord
{
    protected static string $resource = BusResource::class;

    /* PARA TRACCAR */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Crear el registro en la base de datos
        $record = Bus::create($data);

        // Enviar datos a la API de Traccar
        $traccarData = [
            'name' => $data['name'],
            'uniqueId' => $data['uniqueId'],
            'category' => $data['category'],
            'phone' => $data['phone'] ?? null,
            'model' => $data['model'] ?? null,
        ];
        /*  Para entornos de desarrollo se usa sin verificacion de SSL usar la primera opcion para entornos de producción*/

        /* con verificación SSL */
       /*  $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode('milenkaelisaq95@gmail.com:71256293')
        ])->post('https://tu-servidor-traccar/api/devices', $traccarData); */

        /* Sin verificación SSL */

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode('milenkaelisaq95@gmail.com:71256293')
        ])->withoutVerifying()->post('https://demo.traccar.org/api/devices', $traccarData); //para pruebas estan sin verificacion

        if ($response->failed()) {
            throw new \Exception('Error al registrar el dispositivo en Traccar: ' . $response->body());
        }

        return $record;
    }
}
