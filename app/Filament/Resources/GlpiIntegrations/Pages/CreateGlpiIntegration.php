<?php

namespace App\Filament\Resources\GlpiIntegrations\Pages;

use App\Filament\Resources\GlpiIntegrations\GlpiIntegrationResource;
use App\Models\GlpiIntegration;
use Filament\Resources\Pages\CreateRecord;

class CreateGlpiIntegration extends CreateRecord
{
    protected static string $resource = GlpiIntegrationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['active'] ?? false) === true) {
            GlpiIntegration::query()->update([
                'active' => false,
            ]);
        }

        return $data;
    }
}