<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SouvenirResource\Pages;
use App\Filament\Resources\SouvenirResource\RelationManagers;
use App\Models\Souvenir;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SouvenirResource extends Resource
{
    protected static ?string $model = Souvenir::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')->relationship('creator', 'name')->required(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('memoryType.title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('creator.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('memory_points')->sortable()->searchable(),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\EntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSouvenirs::route('/'),
            'create' => Pages\CreateSouvenir::route('/create'),
            'edit' => Pages\EditSouvenir::route('/{record}/edit'),
        ];
    }
}
