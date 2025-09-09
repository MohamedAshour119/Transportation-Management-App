<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
            
            TextColumn::make('email')
                ->searchable()
                ->sortable()
                ->copyable(),
            
            TextColumn::make('phone')
                ->searchable()
                ->toggleable(),
            
            TextColumn::make('website')
                ->url(fn ($record) => $record->website)
                ->openUrlInNewTab()
                ->toggleable(),
            
            TextColumn::make('drivers_count')
                ->counts('drivers')
                ->label('Drivers')
                ->badge()
                ->color('info'),
            
            TextColumn::make('vehicles_count')
                ->counts('vehicles')
                ->label('Vehicles')
                ->badge()
                ->color('success'),
            
            TextColumn::make('trips_count')
                ->counts('trips')
                ->label('Trips')
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
                Filter::make('has_drivers')
                    ->query(fn (Builder $query): Builder => $query->has('drivers'))
                    ->label('Has Drivers'),
                
                Filter::make('has_vehicles')
                    ->query(fn (Builder $query): Builder => $query->has('vehicles'))
                    ->label('Has Vehicles'),
                
                Filter::make('created_this_month')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('created_at', now()->month))
                    ->label('Created This Month'),
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
            ]);
    }
}
