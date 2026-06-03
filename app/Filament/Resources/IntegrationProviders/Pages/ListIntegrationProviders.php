<?php

namespace App\Filament\Resources\IntegrationProviders\Pages;

use App\Filament\Resources\IntegrationProviders\IntegrationProviderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIntegrationProviders extends ListRecords
{
    protected static string $resource = IntegrationProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
