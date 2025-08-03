<?php

namespace App\Filament\Resources\MemoryTypeResource\Pages;

use App\Filament\Resources\MemoryTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemoryTypes extends ListRecords
{
    protected static string $resource = MemoryTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
