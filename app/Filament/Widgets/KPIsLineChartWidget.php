<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class KPIsLineChartWidget extends ChartWidget
{
    protected ?string $heading = 'KPIs Summary';
    protected ?string $pollingInterval = '30s';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $now = Carbon::now();

        $ttl = 60; // seconds
        [$activeTripsCount, $availableDriversCount, $availableVehiclesCount, $completedThisMonthCount] = Cache::remember(
            'kpis.line_chart.stats',
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
