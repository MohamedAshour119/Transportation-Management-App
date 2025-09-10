<?php

namespace App\Filament\Resources\Drivers\Tables;

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

class DriversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
            
                TextColumn::make('company.name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
            
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
            
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
            
                TextColumn::make('license_number')
                    ->searchable()
                    ->toggleable(),
            
                TextColumn::make('license_expiry_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->license_expiry_date && $record->license_expiry_date < now()->addMonths(3) ? 'danger' : 'success')
                    ->toggleable(),
            
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
                    ->searchable()
                    ->preload(),
            
                Filter::make('license_expiring_soon')
                    ->query(fn (Builder $query): Builder => $query->where('license_expiry_date', '<=', now()->addMonths(3)))
                    ->label('License Expiring Soon'),
                
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
