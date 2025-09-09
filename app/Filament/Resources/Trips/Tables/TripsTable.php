<?php

namespace App\Filament\Resources\Trips\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('driver.full_name')
                    ->label('Driver')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle')
                    ->searchable()
                    ->formatStateUsing(fn ($record) => $record->vehicle ? "{$record->vehicle->make} {$record->vehicle->model} ({$record->vehicle->plate_number})" : '-')
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'primary',
                        'scheduled' => 'info',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', (string) $state)))
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Start')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('end_time')
                    ->label('End')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('distance')
                    ->label('Distance')
                    ->sortable()
                    ->suffix(' km')
                    ->toggleable(),

                TextColumn::make('fuel_cost')
                    ->label('Fuel Cost')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('insurance_cost')
                    ->label('Insurance')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('maintenance_cost')
                    ->label('Maintenance')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->money('USD')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'scheduled' => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Filter::make('active_now')
                    ->label('Active Now')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('status', 'in_progress')
                        ->where('start_time', '<=', now())
                        ->where('end_time', '>=', now())
                ),
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
