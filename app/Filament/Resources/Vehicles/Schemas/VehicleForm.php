<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('make')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(date('Y') + 1),
                
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                
                TextInput::make('capacity')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(255)
                    ->suffix('passengers'),
                
                TextInput::make('plate_number')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                TextInput::make('vehicle_identification_number')
                    ->label('VIN')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
