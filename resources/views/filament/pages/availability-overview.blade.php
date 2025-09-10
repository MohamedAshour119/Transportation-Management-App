<x-filament-panels::page>
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 shadow-lg">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    <circle cx="9" cy="12" r="1" fill="currentColor"></circle>
                    <circle cx="15" cy="12" r="1" fill="currentColor"></circle>
                    <circle cx="12" cy="16" r="1" fill="currentColor"></circle>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-white mb-2">Availability Overview</h3>
                <p class="text-slate-300 leading-relaxed">
                    View and analyze driver and vehicle availability within a chosen date range.
                </p>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <x-filament::section>
            <x-slot name="heading">Select time range</x-slot>
            <x-slot name="description">Pick the start and end times then click Check availability.</x-slot>

            <form wire:submit.prevent="checkAvailability" class="space-y-4">
                {{ $this->form }}

                <div>
                    <x-filament::button type="submit" color="primary" icon="heroicon-m-magnifying-glass">
                        Check availability
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2 gap-y-10">
        <x-filament::section>
            <x-slot name="heading">Available drivers</x-slot>

            @if (!empty($availableDrivers))
                <div class="overflow-hidden rounded-lg border border-slate-700">
                    <table class="min-w-full divide-y divide-slate-700">
                        <thead class="bg-slate-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-slate-300">Driver</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-slate-300">Company</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                            @foreach ($availableDrivers as $driver)
                                <tr>
                                    <td class="px-4 py-2 text-slate-200">{{ $driver['full_name'] ?? '—' }}</td>
                                    <td class="px-4 py-2 text-slate-400">{{ $driver['company']['name'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-slate-400">No available drivers for the selected period.</p>
            @endif
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Available vehicles</x-slot>

            @if (!empty($availableVehicles))
                <div class="overflow-hidden rounded-lg border border-slate-700">
                    <table class="min-w-full divide-y divide-slate-700">
                        <thead class="bg-slate-800">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-slate-300">Vehicle</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-slate-300">Plate</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-slate-300">Company</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                            @foreach ($availableVehicles as $vehicle)
                                <tr>
                                    <td class="px-4 py-2 text-slate-200">{{ ($vehicle['make'] ?? '') . ' ' . ($vehicle['model'] ?? '') }}</td>
                                    <td class="px-4 py-2 text-slate-400">{{ $vehicle['plate_number'] ?? '—' }}</td>
                                    <td class="px-4 py-2 text-slate-400">{{ $vehicle['company']['name'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-slate-400">No available vehicles for the selected period.</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>