<?php

namespace App\Filament\Resources\RadicadoResource\Pages;

use App\Filament\Resources\RadicadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRadicado extends EditRecord
{
    protected static string $resource = RadicadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
