<?php

namespace App\Filament\Resources\SoporteResource\Pages;

use App\Filament\Resources\SoporteResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewSoporte extends ViewRecord
{
    protected static string $resource = SoporteResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Radicado')->schema([
                TextEntry::make('radicado.numero')->label('Número')->copyable(),
                TextEntry::make('radicado.modulo')->badge(),
                TextEntry::make('radicado.user.name')->label('Radicado por'),
                TextEntry::make('radicado.created_at')->dateTime('d/m/Y H:i')->label('Creado'),
            ])->columns(2),

            Section::make('Soporte')->schema([
                TextEntry::make('titulo'),
                TextEntry::make('descripcion')->columnSpanFull(),
                TextEntry::make('estado')->badge(),
                TextEntry::make('prioridad')->badge(),
                TextEntry::make('telefono'),
                TextEntry::make('numero_documento')->label('Documento'),
            ])->columns(2),
        ]);
    }
}
