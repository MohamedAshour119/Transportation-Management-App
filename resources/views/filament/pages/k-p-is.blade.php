<x-filament-panels::page>
    <div class="bg-slate-800 border border-slate-700 rounded-lg p-6 shadow-lg">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-white mb-2">KPIs Overview</h3>
                <p class="text-slate-300 leading-relaxed">
                    A quick overview of key transportation metrics, including active trips, available drivers and vehicles, and completed trips this month.
                </p>
            </div>
        </div>
    </div>
    {{-- KPIs widget below the description --}}
    <div class="mt-6">
        <x-filament-widgets::widgets
            :widgets="[\App\Filament\Widgets\KPIsWidget::class]"
        />
    </div>
    <div class="mt-6">
        <x-filament-widgets::widgets
            :widgets="[\App\Filament\Widgets\KPIsLineChartWidget::class]"
        />
    </div>
</x-filament-panels::page>