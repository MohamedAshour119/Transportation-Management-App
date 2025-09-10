<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Trip;
use App\Models\Company;
use Carbon\Carbon;

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
            
                TextInput::make('address')
                ->columnSpanFull(),

            Section::make('Assign drivers to this company')
                ->description('Pick from drivers that are not assigned to any company and attach them to this company when you save.')
                ->schema([
                    Select::make('assign_driver_ids')
                        ->label('Unassigned drivers')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(fn () => \App\Models\Driver::query()
                            ->whereNull('company_id')
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->toArray()
                        )
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->columnSpanFull(),

            Section::make('Assigned drivers to this company')
                ->description('Pick from drivers that are not assigned to any company and attach them to this company when you save.')
                ->schema([
                    Select::make('assigned_driver_ids')
                        ->label('Assigned drivers')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(function ($livewire) {
                            $record = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;
                            if (! $record) {
                                return [];
                            }
                            return \App\Models\Driver::query()
                                ->where('company_id', $record->id)
                                ->orderBy('full_name')
                                ->pluck('full_name', 'id')
                                ->toArray();
                        })
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->columnSpanFull(),
            ]);
    }
}
