<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Trip;
use Carbon\Carbon;

class KPIsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $now = Carbon::now();

        // Active trips in progress now
        $activeTripsCount = Trip::getActiveTrips()->count();

        // Available drivers and vehicles right now (no overlapping trips at this instant)
        $availableDriversCount = Trip::getAvailableDrivers($now, $now)->count();
        $availableVehiclesCount = Trip::getAvailableVehicles($now, $now)->count();

        // Trips completed in the current month
        $completedThisMonthCount = Trip::getTripsCompletedThisMonth()->count();

        return [
            Stat::make('Active Trips', (string) $activeTripsCount)
                ->description('Trips currently in progress')
                ->icon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('Available Drivers', (string) $availableDriversCount)
                ->description('Drivers free to take trips now')
                ->icon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Available Vehicles', (string) $availableVehiclesCount)
                ->description('Vehicles free to dispatch now')
                ->icon('heroicon-m-truck')
                ->color('success'),

            Stat::make('Completed This Month', (string) $completedThisMonthCount)
                ->description('Trips finished this month')
                ->icon('heroicon-m-check-circle')
                ->color('primary'),
        ];
    }
}
