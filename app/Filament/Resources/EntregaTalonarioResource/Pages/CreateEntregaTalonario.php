<?php

namespace App\Filament\Resources\EntregaTalonarioResource\Pages;

use App\Filament\Resources\EntregaTalonarioResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;


use Illuminate\Database\QueryException;
use Filament\Notifications\Notification;

class CreateEntregaTalonario extends CreateRecord
{
    protected static string $resource = EntregaTalonarioResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return static::getModel()::create($data);
        } catch (QueryException $e) {
            $mensaje = $e->getMessage();

            // Verificar si es un error del TRIGGER
            if (str_contains($mensaje, 'Error: No hay suficientes talonarios')) {
                Notification::make()
                    ->title('Error al entregar talonarios')
                    ->body($mensaje)
                    ->danger()
                    ->send();
            } else {
                Notification::make()
                    ->title('Error inesperado')
                    ->body('No se pudo crear el registro. Revisa los datos ingresados.')
                    ->danger()
                    ->send();
            }

            // Aquí puedes lanzar una excepción personalizada o manejar el error de otra manera
            // Retornar un valor nulo o lanzar una excepción, dependiendo de tu necesidad
            throw new \Exception('Error al crear el registro'); // O podrías retornar un valor por defecto, según lo que necesites.
        }
    }
}
