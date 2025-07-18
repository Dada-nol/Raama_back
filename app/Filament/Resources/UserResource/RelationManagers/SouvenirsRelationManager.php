<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Souvenir;
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
                Tables\Columns\TextColumn::make('pivot.role'),
                Tables\Columns\TextColumn::make('pivot.joined_at')->label('Joined At'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\Action::make('attachSouvenirUser')
                    ->label('Gérer les membres des souvenirs')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
