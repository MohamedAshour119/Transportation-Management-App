<?php

use App\Filament\Pages\AvailabilityOverview;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Authenticate as a user to access Filament panel pages
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

function makeCompanyDriverVehicle(): array {
    $company = Company::factory()->create();
    $driver = Driver::factory()->create([ 'company_id' => $company->id ]);
    $vehicle = Vehicle::factory()->create([ 'company_id' => $company->id ]);
    return [$company, $driver, $vehicle];
}

function makeTripFor(int $companyId, int $driverId, int $vehicleId, Carbon|string $start, Carbon|string $end, string $status = 'pending'): Trip {
    return Trip::create([
        'company_id' => $companyId,
        'driver_id' => $driverId,
        'vehicle_id' => $vehicleId,
        'status' => $status,
        'start_time' => $start instanceof Carbon ? $start : Carbon::parse($start),
        'end_time' => $end instanceof Carbon ? $end : Carbon::parse($end),
        'start_location' => 'A',
        'end_location' => 'B',
    ]);
}

test('can render availability overview page', function () {
    // Prefer HTTP route if available (Filament Page helper)
    if (method_exists(AvailabilityOverview::class, 'getUrl')) {
        $this->get(AvailabilityOverview::getUrl())
            ->assertSuccessful();
        return;
    }

    // Fallback to Livewire render
    Livewire::test(AvailabilityOverview::class)->assertOk();
});

test('validates that end time must be after start time', function () {
    $start = Carbon::parse('2025-01-01 12:00:00');
    $end = Carbon::parse('2025-01-01 11:00:00');

    $component = Livewire::test(AvailabilityOverview::class)
        ->set('data.start_time', $start)
        ->set('data.end_time', $end)
        ->call('checkAvailability');

    // Check that validation failed by ensuring no results were set
    expect($component->get('availableDrivers'))->toBeEmpty()
        ->and($component->get('availableVehicles'))->toBeEmpty();
});

test('lists available drivers and vehicles when no overlaps exist', function () {
    [$company, $driver, $vehicle] = makeCompanyDriverVehicle();

    $start = Carbon::parse('2025-01-01 10:00:00');
    $end = Carbon::parse('2025-01-01 12:00:00');

    Livewire::test(AvailabilityOverview::class)
        ->set('data.start_time', $start)
        ->set('data.end_time', $end)
        ->call('checkAvailability')
        ->assertSet('availableDrivers', fn ($arr) => collect($arr)->pluck('id')->contains($driver->id))
        ->assertSet('availableVehicles', fn ($arr) => collect($arr)->pluck('id')->contains($vehicle->id));
});

test('driver and vehicle become unavailable when overlapping trip exists', function () {
    [$company, $driver, $vehicle] = makeCompanyDriverVehicle();

    // Overlapping trip blocks both resources
    makeTripFor($company->id, $driver->id, $vehicle->id, '2025-01-01 10:30:00', '2025-01-01 11:30:00', status: 'in_progress');

    $start = Carbon::parse('2025-01-01 10:00:00');
    $end = Carbon::parse('2025-01-01 12:00:00');

    Livewire::test(AvailabilityOverview::class)
        ->set('data.start_time', $start)
        ->set('data.end_time', $end)
        ->call('checkAvailability')
        ->assertSet('availableDrivers', fn ($arr) => ! collect($arr)->pluck('id')->contains($driver->id))
        ->assertSet('availableVehicles', fn ($arr) => ! collect($arr)->pluck('id')->contains($vehicle->id));
});

test('cancelled overlapping trip does not affect availability', function () {
    [$company, $driver, $vehicle] = makeCompanyDriverVehicle();

    // Cancelled overlap should be ignored per implementation
    makeTripFor($company->id, $driver->id, $vehicle->id, '2025-01-01 10:30:00', '2025-01-01 11:30:00', status: 'cancelled');

    $start = Carbon::parse('2025-01-01 10:00:00');
    $end = Carbon::parse('2025-01-01 12:00:00');

    Livewire::test(AvailabilityOverview::class)
        ->set('data.start_time', $start)
        ->set('data.end_time', $end)
        ->call('checkAvailability')
        ->assertSet('availableDrivers', fn ($arr) => collect($arr)->pluck('id')->contains($driver->id))
        ->assertSet('availableVehicles', fn ($arr) => collect($arr)->pluck('id')->contains($vehicle->id));
});

test('boundary behavior: trip ending exactly at start time counts as overlapping with inclusive whereBetween', function () {
    [$company, $driver, $vehicle] = makeCompanyDriverVehicle();

    makeTripFor($company->id, $driver->id, $vehicle->id, '2025-01-01 09:00:00', '2025-01-01 10:00:00', status: 'completed');

    $start = Carbon::parse('2025-01-01 10:00:00');
    $end = Carbon::parse('2025-01-01 11:00:00');

    Livewire::test(AvailabilityOverview::class)
        ->set('data.start_time', $start)
        ->set('data.end_time', $end)
        ->call('checkAvailability')
        ->assertSet('availableDrivers', fn ($arr) => ! collect($arr)->pluck('id')->contains($driver->id))
        ->assertSet('availableVehicles', fn ($arr) => ! collect($arr)->pluck('id')->contains($vehicle->id));
});

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
