<?php

namespace App\Filament\Resources\SouvenirResource\Pages;

use App\Filament\Resources\SouvenirResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSouvenir extends CreateRecord
{
    protected static string $resource = SouvenirResource::class;

    protected function afterCreate(): void
    {
        $userId = $this->data['user_id'] ?? null;
        if ($userId) {
            $this->record->users()->attach($userId, ['role' => 'admin']);
        }
    }
}
