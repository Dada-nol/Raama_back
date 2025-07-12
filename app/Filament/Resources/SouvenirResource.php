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
                //        'name',
                // 'description',
                // 'cover_image',
                // 'is_closed'
                Forms\Components\TextInput::make('name')->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cover_image')->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_closed')
                    ->label('Souvenir fermé ?')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('is_closed')
                    ->label('Statut')
                    ->formatStateUsing(fn(bool $state) => $state ? 'Fermé' : 'Ouvert')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
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
