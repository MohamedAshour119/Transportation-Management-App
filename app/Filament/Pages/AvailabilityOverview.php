<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use App\Models\Trip;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;

class AvailabilityOverview extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.availability-overview';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    public ?array $data = [];
    public array $availableDrivers = [];
    public array $availableVehicles = [];

    public function getHeading(): string
    {
        return '';
    }

    public function mount(): void
    {
        $now = Carbon::now();
        $this->form->fill([
            'start_time' => $now,
            'end_time' => $now->copy()->addHours(2),
        ]);

        // Show initial results on first load
        $this->checkAvailability();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DateTimePicker::make('start_time')
                    ->label('Start time')
                    ->seconds(false)
                    ->native(false)
                    ->required(),
                DateTimePicker::make('end_time')
                    ->label('End time')
                    ->seconds(false)
                    ->native(false)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function checkAvailability(): void
    {
        $this->validate([
            'data.start_time' => ['required', 'date'],
            'data.end_time' => ['required', 'date', 'after:data.start_time'],
        ]);

        $start = Carbon::parse($this->data['start_time']);
        $end = Carbon::parse($this->data['end_time']);

        $this->availableDrivers = Trip::getAvailableDrivers($start, $end)
            ->select(['id', 'full_name', 'company_id'])
            ->with('company:id,name')
            ->orderBy('full_name')
            ->get()
            ->toArray();

        $this->availableVehicles = Trip::getAvailableVehicles($start, $end)
            ->select(['id', 'make', 'model', 'plate_number', 'company_id'])
            ->with('company:id,name')
            ->orderBy('make')
            ->orderBy('model')
            ->get()
            ->toArray();
    }
}
