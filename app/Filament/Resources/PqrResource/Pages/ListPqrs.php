<?php

namespace App\Filament\Resources\PqrResource\Pages;

use App\Filament\Resources\PqrResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPqrs extends ListRecords
{
    protected static string $resource = PqrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
