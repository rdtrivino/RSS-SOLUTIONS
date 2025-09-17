<?php

namespace App\Filament\Resources\SoporteResource\Pages;

use App\Filament\Resources\SoporteResource;
use Filament\Resources\Pages\ListRecords;

class ListSoportes extends ListRecords
{
    protected static string $resource = SoporteResource::class;
    protected static ?string $title = 'Soportes';
}
