<?php

namespace App\Filament\Resources\RadicadoResource\Pages;

use App\Filament\Resources\RadicadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRadicados extends ListRecords
{
    protected static string $resource = RadicadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
