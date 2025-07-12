<?php

use Illuminate\Support\Facades\Route;
use Filament\Notifications\Notification;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test-filament', function () {
    Notification::make()
        ->title('Test de notification')
        ->body('Si tu vois ça, Filament est chargé correctement.')
        ->success()
        ->send();

    return 'Notification envoyée (si Filament fonctionne)';
});
