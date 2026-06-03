<?php

namespace App\Filament\Resources\IntegrationProviders\Pages;

use App\Filament\Resources\IntegrationProviders\IntegrationProviderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIntegrationProvider extends EditRecord
{
    protected static string $resource = IntegrationProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
