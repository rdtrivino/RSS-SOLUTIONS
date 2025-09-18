<?php

namespace App\Filament\Resources\FacturaResource\Pages;

use App\Filament\Resources\FacturaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFactura extends ViewRecord
{
    protected static string $resource = FacturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Solo lectura desde esta página. Si luego quieres editar notas,
            // quita ->disabled() y ajusta el form del recurso.
            Actions\EditAction::make()->disabled(),
        ];
    }
}
