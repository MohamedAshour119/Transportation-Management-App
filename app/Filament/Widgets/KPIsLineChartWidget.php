<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Trip;
use Carbon\Carbon;

class KPIsLineChartWidget extends ChartWidget
{
    protected ?string $heading = 'KPIs Summary';
    protected ?string $pollingInterval = '30s';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $now = Carbon::now();

        $activeTripsCount = Trip::getActiveTrips()->count();
        $availableDriversCount = Trip::getAvailableDrivers($now, $now)->count();
        $availableVehiclesCount = Trip::getAvailableVehicles($now, $now)->count();
        $completedThisMonthCount = Trip::getTripsCompletedThisMonth()->count();

        return [
            'labels' => [
                'Active Trips',
                'Available Drivers',
                'Available Vehicles',
                'Completed This Month',
            ],
            'datasets' => [
                [
                    'label' => 'Count',
                    'data' => [
                        $activeTripsCount,
                        $availableDriversCount,
                        $availableVehiclesCount,
                        $completedThisMonthCount,
                    ],
                    'backgroundColor' => [
                        '#60a5fa', // Active Trips - blue
                        '#34d399', // Available Drivers - green
                        '#34d399', // Available Vehicles - green
                        '#8b5cf6', // Completed This Month - purple
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
