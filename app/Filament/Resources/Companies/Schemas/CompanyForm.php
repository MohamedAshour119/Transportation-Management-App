<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            
            TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            
            TextInput::make('phone')
                ->tel()
                ->maxLength(255),
            
            TextInput::make('website')
                ->url()
                ->maxLength(255)
                ->columnSpanFull(),
            
            Textarea::make('address')
                ->rows(3)
                ->columnSpanFull(),
            ]);
    }
}
