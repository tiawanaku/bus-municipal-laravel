<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class LeafletMap extends Field
{
    protected string $view = 'forms.components.leaflet-map';

    public function pointField(string $field): static
    {
        return $this->extraAttributes(['data-point-field' => $field]);
    }

    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }
}