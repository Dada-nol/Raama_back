<?php

namespace App\Filament\Resources\MemoryTypeResource\Pages;

use App\Filament\Resources\MemoryTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemoryType extends EditRecord
{
    protected static string $resource = MemoryTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
