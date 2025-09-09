<?php

namespace App\Filament\Resources\Trips\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Trip Details')
                ->schema([
                    Select::make('company_id')
                        ->relationship('company', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function (Set $set) {
                            // Reset dependent selects when company changes
                            $set('driver_id', null);
                            $set('vehicle_id', null);
                        }),

                    Select::make('driver_id')
                        ->label('Driver')
                        ->relationship('driver', 'full_name', modifyQueryUsing: function (Builder $query, Get $get) {
                            if ($get('company_id')) {
                                $query->where('company_id', $get('company_id'));
                            }
                        })
                        ->required()
                        ->searchable()
                        ->preload(),

                    Select::make('vehicle_id')
                        ->label('Vehicle')
                        ->relationship('vehicle', 'plate_number', modifyQueryUsing: function (Builder $query, Get $get) {
                            if ($get('company_id')) {
                                $query->where('company_id', $get('company_id'));
                            }
                        })
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->make} {$record->model} ({$record->plate_number})")
                        ->required()
                        ->searchable()
                        ->preload(),

                    Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'scheduled' => 'Scheduled',
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required()
                        ->default('pending'),
                ])
                ->columnSpanFull(),

                Section::make('Schedule & Location')
                ->schema([
                    DateTimePicker::make('start_time')
                        ->required()
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            if ($state) {
                                $set('end_time', null);
                            }
                        }),

                    DateTimePicker::make('end_time')
                        ->required()
                        ->native(false)
                        ->after('start_time'),

                    TextInput::make('start_location')
                        ->label('Start Location')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('end_location')
                        ->label('End Location')
                        ->required()
                        ->maxLength(255),
                ])
                ->columnSpanFull(),

                Section::make('Trip Metrics')
                ->schema([
                    TextInput::make('distance')
                        ->numeric()
                        ->step('0.01')
                        ->suffix(' km'),

                    TextInput::make('fuel_consumption')
                        ->numeric()
                        ->step('0.01')
                        ->suffix(' L'),

                    TextInput::make('fuel_price')
                        ->numeric()
                        ->step('0.01')
                        ->prefix('$')
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            $consumption = (float) ($get('fuel_consumption') ?? 0);
                            $price = (float) ($state ?? 0);
                            $set('fuel_cost', $consumption && $price ? round($price * $consumption, 2) : null);
                        }),

                    TextInput::make('fuel_cost')
                        ->numeric()
                        ->step('0.01')
                        ->prefix('$')
                        ->readOnly(),
                ])
                ->columnSpanFull(),

                Section::make('Additional Costs')
                ->schema([
                    TextInput::make('insurance_cost')
                        ->numeric()
                        ->step('0.01')
                        ->prefix('$')
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            $set('total_cost',
                                (float) ($get('fuel_cost') ?? 0)
                                + (float) ($get('insurance_cost') ?? 0)
                                + (float) ($get('maintenance_cost') ?? 0)
                            );
                        }),

                    TextInput::make('maintenance_cost')
                        ->numeric()
                        ->step('0.01')
                        ->prefix('$')
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            $set('total_cost',
                                (float) ($get('fuel_cost') ?? 0)
                                + (float) ($get('insurance_cost') ?? 0)
                                + (float) ($get('maintenance_cost') ?? 0)
                            );
                        }),

                    TextInput::make('total_cost')
                        ->numeric()
                        ->step('0.01')
                        ->prefix('$')
                        ->readOnly(),
                ])
                ->columnSpanFull(),
            ]);
    }
}
