<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Souvenir;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Builder;

class SouvenirsRelationManager extends RelationManager
{
    protected static string $relationship = 'souvenirs';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            //
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('pivot.role')->label('Role dans le souvenir'),
                Tables\Columns\TextColumn::make('pivot.joined_at')->label('Joined At'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\Action::make('attachSouvenirUser')
                    ->label('Rejoindre un souvenir')
                    ->form([
                        Forms\Components\Select::make('souvenir_id')
                            ->label('Souvenir')
                            ->options(
                                Souvenir::all()->pluck('title', 'id')
                            )
                            ->required()->live(),

                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'member' => 'Member'
                            ])->nullable()
                            ->label('Role'),
                        Forms\Components\DateTimePicker::make('joined_at')
                            ->label('Rejoins le'),
                    ])
                    ->action(function (array $data) {
                        $this->getOwnerRecord()->souvenirs()->attach($data['souvenir_id'], [
                            'role' => $data['role'] ?? null,
                            'joined_at' => $data['joined_at'] ?? null,
                        ]);
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->label('Modifier')
                    ->form([

                        Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'member' => 'Member'
                            ])->nullable()
                            ->label('Role'),

                        Forms\Components\DateTimePicker::make('joined_at')
                            ->label('Rejoins le')
                            ->nullable(),
                    ])
                    ->mutateFormDataUsing(function (array $data) {
                        return [
                            'pivot.role' => $data['role'] ?? false,
                            'pivot.joined_at' => $data['joined_at'] ?? null,
                        ];
                    })
                    ->using(function ($record, $data, $livewire) {
                        $livewire->getOwnerRecord()
                            ->souvenirs()
                            ->updateExistingPivot($record->id, [
                                'role' => $data['pivot.role'],
                                'joined_at' => $data['pivot.joined_at'],
                            ]);
                    }),

                DeleteAction::make()
                    ->label('Détacher')
                    ->action(function ($record, $livewire) {
                        $livewire->getOwnerRecord()
                            ->souvenirs()
                            ->detach($record->id);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
