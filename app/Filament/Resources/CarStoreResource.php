<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarStoreResource\Pages;
use App\Filament\Resources\CarStoreResource\RelationManagers;
use App\Filament\Resources\CarStoreResource\RelationManagers\PhotosRelationManager;
use App\Models\CarStore;
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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Filters\SelectFilter;

class CarStoreResource extends Resource
{
    protected static ?string $model = CarStore::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('phone_number')->required()->maxLength(255),
                TextInput::make('cs_name')->required()->maxLength(255),
                Select::make('is_open')->options([true=>'open',false=>'close'])->required(),
                Select::make('is_full')->options([true=>'full booked',false=>'available'])->required(),
                Select::make('id_city')->relationship('city', 'name')->searchable()->preload()->required(),
                Repeater::make('storeServices')->relationship()->schema([
                    Select::make('car_service_id')->relationship('carService','name')->required(),
                ]),
                FileUpload::make('thumbnail')->image()->required(),
                Textarea::make('address')->rows(10)->cols(20)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                Tables\Columns\IconColumn::make('is_open')->boolean()->trueColor('success')->falseColor('danger')->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\IconColumn::make('is_full')->boolean()->trueColor('danger')->falseColor('success')->trueIcon('heroicon-o-x-circle')->falseIcon('heroicon-o-check-circle'),
                Tables\Columns\ImageColumn::make('thumbnail'),
            ])
            ->filters([
                SelectFilter::make('id_city')->label('City')->relationship('city', 'name'),
                SelectFilter::make('car_service_id')->label('Services')->options(CarService::pluck('name', 'id'))->query(function (Builder $query, array $data) {
                    if($data['value']){
                        $query->whereHas('storeServices', function($query) use ($data) {
                            $query->where('car_service_id', $data['value']);
                        });
                    }
                }),
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
            PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarStores::route('/'),
            'create' => Pages\CreateCarStore::route('/create'),
            'edit' => Pages\EditCarStore::route('/{record}/edit'),
        ];
    }
}
