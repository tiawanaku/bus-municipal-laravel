<?php

namespace App\Filament\Resources\BusResource\Pages;

use App\Filament\Resources\BusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Http;

class EditBus extends EditRecord
{
    protected static string $resource = BusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];


    }
    /* Consulta a la Api de Traccar por el uniqueId y trae los datos almacenados en Traccar */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['uniqueId'])) {
            // Consultar API de Traccar

            /*  Para entornos de desarrollo se usa sin verificacion de SSL, usar la primera opcion para entornos de producción*/
            /* $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode('milenkaelisaq95@gmail.com:71256293') 
            ])->get("https://demo.traccar.org/api/devices?uniqueId={$data['uniqueId']}"); */

            /* Sin verificación SSL */
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode('milenkaelisaq95@gmail.com:71256293')
            ])->withoutVerifying()->get("https://demo.traccar.org/api/devices?uniqueId={$data['uniqueId']}");

            if ($response->successful()) {
                $deviceData = collect($response->json())->first(); // Obtener primer dispositivo coincidente

                if ($deviceData) {
                    // Agregar los datos obtenidos al formulario
                    $data['name'] = $deviceData['name'];
                    $data['model'] = $deviceData['model'] ?? 'Desconocido';
                    $data['phone'] = $deviceData['phone'] ?? 'No disponible';
                    $data['category'] = $deviceData['category'];
                }
            }
        }

        return $data;
    }

/* Función para guardar en caso que sea nuevo GPS */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
{
    if ($data['nuevoGPS'] ?? false) {
        // Usuario quiere registrar un nuevo GPS en Traccar
        $traccarData = [
            'name' => $data['name'],
            'uniqueId' => $data['uniqueIdManual'],
            'model' => $data['model'],
            'category' => $data['category'] ?? 'bus',
            'phone' => $data['phone'] ?? null,
        ];

        // Enviar nuevo GPS a Traccar
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode('milenkaelisaq95@gmail.com:71256293')
        ])->withoutVerifying()->post("https://demo.traccar.org/api/devices", $traccarData);

        if ($response->failed()) {
            throw new \Exception('Error al registrar el dispositivo en Traccar: ' . $response->body());
        }

        // Si el registro en Traccar fue exitoso, actualizamos el bus con el nuevo Unique ID
        $record->update([
            'uniqueId' => $data['uniqueIdManual']
        ]);
    }

    return parent::handleRecordUpdate($record, $data);
}

}
