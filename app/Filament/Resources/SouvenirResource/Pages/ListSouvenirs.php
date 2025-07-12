<?php

namespace App\Filament\Resources\SouvenirResource\Pages;

use App\Filament\Resources\SouvenirResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSouvenirs extends ListRecords
{
    protected static string $resource = SouvenirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
