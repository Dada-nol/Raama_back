<?php

namespace App\Filament\Resources\SouvenirResource\Pages;

use App\Filament\Resources\SouvenirResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSouvenir extends EditRecord
{
    protected static string $resource = SouvenirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
