<?php

namespace App\Filament\Resources\SalidaDeBusesResource\Widgets;

use Filament\Widgets\Widget;


class MapWidget extends Widget
{
    protected static string $view = 'filament.resources.salida-de-buses-resource.widgets.map-widget';

    public function getViewData(): array
    {
        return [
            'iframeUrl' => 'http://127.0.0.3:8000/tracking-data/',
                
        ];
    }
}
