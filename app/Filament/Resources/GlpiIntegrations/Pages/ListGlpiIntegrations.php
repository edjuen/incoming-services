<?php

namespace App\Filament\Resources\GlpiIntegrations\Pages;

use App\Filament\Resources\GlpiIntegrations\GlpiIntegrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGlpiIntegrations extends ListRecords
{
    protected static string $resource = GlpiIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva integración GLPI'),
        ];
    }
}