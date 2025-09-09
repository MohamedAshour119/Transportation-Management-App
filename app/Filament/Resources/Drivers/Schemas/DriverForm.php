<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Driver Information')
                ->schema([
                    TextInput::make('full_name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    
                    Select::make('company_id')
                        ->relationship('company', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    
                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(255),
                    
                    Textarea::make('address')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

                Section::make('License Information')
                ->schema([
                    TextInput::make('license_number')
                        ->maxLength(255),
                    
                    DatePicker::make('license_expiry_date')
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                ])
                ->columnSpanFull()
            ]);
    }
}
