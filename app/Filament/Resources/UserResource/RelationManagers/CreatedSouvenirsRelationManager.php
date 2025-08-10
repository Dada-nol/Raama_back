<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreatedSouvenirsRelationManager extends RelationManager
{
    protected static string $relationship = 'createdSouvenirs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('memory_type_id')->relationship('memoryType', 'title')->required(),
                Forms\Components\TextInput::make('title')->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('cover_image')
                    ->label('Image de fond')
                    ->directory('souvenirs/covers')
                    ->image()
                    ->preserveFilenames()
                    ->visibility('public')
                    ->disk('public'),
                Forms\Components\TextInput::make('memory_points')->numeric()->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('memoryType.title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('memory_points')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function ($record, $data) {
                        // Attacher le user propriétaire (ownerRecord) dans la pivot souvenir_users
                        $record->users()->attach($this->ownerRecord->id, ['role' => 'admin']);
                    }),
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
