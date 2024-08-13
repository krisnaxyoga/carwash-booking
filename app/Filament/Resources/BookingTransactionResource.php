<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingTransactionResource\Pages;
use App\Filament\Resources\BookingTransactionResource\RelationManagers;
use App\Models\BookingTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TimePicker;

class BookingTransactionResource extends Resource
{
    protected static ?string $model = BookingTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('trx_id')->required(),
                TextInput::make('phone_number')->required(),
                TextInput::make('total_amount')->numeric()->required()->prefix('IDR'),
                DatePicker::make('started_at')->required(),
                TimePicker::make('time_at')->required(),
                Forms\Components\Select::make('is_paid')->options([true=>'paid',false=>'no paid'])->required(),
                Forms\Components\Select::make('car_service_id')->relationship('carService', 'name')->searchable()->preload()->required(),
                Forms\Components\Select::make('car_store_id')->relationship('carStore', 'name')->searchable()->preload()->required(),
                Forms\Components\FileUpload::make('proof')->image()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trx_id')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('carService.name'),
                TextColumn::make('started_at'),
                TextColumn::make('time_at'),
                Tables\Columns\IconColumn::make('is_paid')->label('sudah bayar?')->boolean()->trueColor('success')->falseColor('danger')->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle'),
               
                Tables\Columns\ImageColumn::make('proof'),
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
            'index' => Pages\ListBookingTransactions::route('/'),
            'create' => Pages\CreateBookingTransaction::route('/create'),
            'edit' => Pages\EditBookingTransaction::route('/{record}/edit'),
        ];
    }
}
