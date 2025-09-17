<?php

namespace App\Filament\Staff\Resources\SoporteResource\Pages;

use App\Filament\Staff\Resources\SoporteResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSoporte extends ViewRecord
{
    protected static string $resource = SoporteResource::class;

    protected function getHeaderActions(): array
    {
        // Si quisieras, puedes repetir aquí acciones como descargar PDF
        return [];
    }
}
