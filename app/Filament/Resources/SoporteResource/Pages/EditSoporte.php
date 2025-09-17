<?php

namespace App\Filament\Resources\SoporteResource\Pages;

use App\Filament\Resources\SoporteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoporte extends EditRecord
{
    protected static string $resource = SoporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
