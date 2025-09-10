@php
    use App\Models\Trip;
    use App\Filament\Resources\Trips\TripResource;
    $activeCount = Trip::getActiveTrips()->count();
@endphp

<div class="tma-active-trips">
    <a
        href="{{ TripResource::getUrl('index') }}"
        class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-primary-500 hover:text-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
        title="Active trips"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3">
            <path d="M2.25 12a8.25 8.25 0 1116.5 0 8.25 8.25 0 01-16.5 0zm8.25-4.5a.75.75 0 00-.75.75v3.75c0 .199.079.39.22.53l2.25 2.25a.75.75 0 101.06-1.06l-2.03-2.03V8.25a.75.75 0 00-.75-.75z" />
        </svg>
        <span>Active Trips: {{ $activeCount }}</span>
    </a>
</div>
