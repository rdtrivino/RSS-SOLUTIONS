<?php

namespace App\Filament\Staff\Resources\SoporteResource\Pages;

use App\Filament\Staff\Resources\SoporteResource;
use Filament\Resources\Pages\ListRecords;

class ListSoportes extends ListRecords
{
    protected static string $resource = SoporteResource::class;

    protected function getHeaderActions(): array
    {
        // En staff no se crean soportes manualmente
        return [];
    }
}
