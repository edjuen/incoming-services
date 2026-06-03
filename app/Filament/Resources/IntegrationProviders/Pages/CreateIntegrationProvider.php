<?php

namespace App\Filament\Resources\IntegrationProviders\Pages;

use App\Filament\Resources\IntegrationProviders\IntegrationProviderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIntegrationProvider extends CreateRecord
{
    protected static string $resource = IntegrationProviderResource::class;
}
