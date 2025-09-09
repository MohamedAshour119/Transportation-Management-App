<?php

namespace App\Filament\Resources\Vehicles\Tables;

use App\Models\Vehicle;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('make')
                    ->sortable(),
                
                TextColumn::make('model')
                    ->sortable(),
                
                TextColumn::make('year')
                    ->sortable()
                    ->badge(),
                
                TextColumn::make('company.name')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('capacity')
                    ->suffix(' passengers')
                    ->sortable(),
                
                TextColumn::make('plate_number')
                    ->copyable(),
                
                TextColumn::make('vehicle_identification_number')
                    ->label('VIN')
                    ->toggleable()
                    ->limit(20),
                
                TextColumn::make('trips_count')
                    ->counts('trips')
                    ->label('Total Trips')
                    ->badge()
                    ->color('warning'),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->preload(),
            
                SelectFilter::make('make')
                    ->options(fn () => Vehicle::distinct()->pluck('make', 'make')->toArray()),
                
                Filter::make('high_capacity')
                    ->query(fn (Builder $query): Builder => $query->where('capacity', '>=', 20))
                    ->label('High Capacity (20+)'),
                
                Filter::make('has_trips')
                    ->query(fn (Builder $query): Builder => $query->has('trips'))
                    ->label('Has Trips'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }
}
