<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('souvenir_id')
                    ->label('Souvenir')
                    ->options(function () {
                        return $this->ownerRecord
                            ->souvenirs()
                            ->pluck('title', 'souvenirs.id');
                    })
                    ->required(),
                Forms\Components\FileUpload::make('image_path')
                    ->label('Image')
                    ->directory('souvenirs/entries')
                    ->image()
                    ->preserveFilenames()
                    ->visibility('public')
                    ->disk('public'),
                Forms\Components\TextInput::make('caption')->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable(),
                Tables\Columns\TextColumn::make('souvenir.title'),
                Tables\Columns\TextColumn::make('souvenir.memoryType.title'),
                Tables\Columns\TextColumn::make('caption'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
