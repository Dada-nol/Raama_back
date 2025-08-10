<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemoryTypeResource\Pages;
use App\Filament\Resources\MemoryTypeResource\RelationManagers;
use App\Models\MemoryType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemoryTypeResource extends Resource
{
    protected static ?string $model = MemoryType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\Toggle::make('isAvailable')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('description')->sortable(),
                Tables\Columns\IconColumn::make('isAvailable')->sortable()->boolean(),

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
            'index' => Pages\ListMemoryTypes::route('/'),
            'create' => Pages\CreateMemoryType::route('/create'),
            'edit' => Pages\EditMemoryType::route('/{record}/edit'),
        ];
    }
}
