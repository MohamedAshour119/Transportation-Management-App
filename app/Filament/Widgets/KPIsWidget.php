<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class KPIsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $now = Carbon::now();

        // Cache KPIs for a short duration to reduce query load
        $ttl = 60; // seconds
        [$activeTripsCount, $availableDriversCount, $availableVehiclesCount, $completedThisMonthCount] = Cache::remember(
            'kpis.widget.stats',
            $ttl,
            function () use ($now) {
                $active = Trip::getActiveTrips()->count();
                $availDrivers = Trip::getAvailableDrivers($now, $now)->count();
                $availVehicles = Trip::getAvailableVehicles($now, $now)->count();
                $completedThisMonth = Trip::getTripsCompletedThisMonth()->count();

                return [
                    $active,
                    $availDrivers,
                    $availVehicles,
                    $completedThisMonth,
                ];
            }
        );

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

