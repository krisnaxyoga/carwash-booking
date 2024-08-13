<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarServiceResource\Pages;
use App\Filament\Resources\CarServiceResource\RelationManagers;
use App\Models\CarService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;

class CarServiceResource extends Resource
{
    protected static ?string $model = CarService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->helperText('Name of Service')->required()->maxLength(255),
                TextInput::make('price')->numeric()->prefix('IDR ')->required(),
                TextInput::make('duration_in_hour')->numeric()->required(),
                FileUpload::make('photo')->image()->disk('public'),
                FileUpload::make('icon')->image()->disk('public'),
                RichEditor::make('about'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('slug'),
                TextColumn::make('duration_in_hour'),
                TextColumn::make('price'),
                Tables\Columns\ImageColumn::make('icon'),
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
            'index' => Pages\ListCarServices::route('/'),
            'create' => Pages\CreateCarService::route('/create'),
            'edit' => Pages\EditCarService::route('/{record}/edit'),
        ];
    }
}
