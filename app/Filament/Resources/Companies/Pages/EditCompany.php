<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use App\Models\Driver;
use Filament\Notifications\Notification;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Assign any selected unassigned drivers to this company
        $state = $this->form->getState();
        $driverIds = $state['assign_driver_ids'] ?? [];

        if (! empty($driverIds)) {
            Driver::whereIn('id', $driverIds)->update(['company_id' => $this->record->id]);

            // Optionally clear the UI selection (non-persistent anyway) and notify
            Notification::make()
                ->title('Drivers assigned')
                ->body(count($driverIds) . ' driver(s) were assigned to this company.')
                ->success()
                ->send();
        }
    }
}
