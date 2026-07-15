<?php

namespace App\Filament\Resources\GlpiIntegrations\Pages;

use App\Filament\Resources\GlpiIntegrations\GlpiIntegrationResource;
use App\Models\GlpiIntegration;
use Filament\Resources\Pages\EditRecord;

class EditGlpiIntegration extends EditRecord
{
    protected static string $resource = GlpiIntegrationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['active'] ?? false) === true) {
            GlpiIntegration::query()
                ->whereKeyNot($this->record->id)
                ->update([
                    'active' => false,
                ]);
        }

        return $data;
    }
}
