<?php

namespace App\Filament\Resources\GlpiMigrationLogs\Pages;

use App\Filament\Resources\GlpiMigrationLogs\GlpiMigrationLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGlpiMigrationLog extends EditRecord
{
    protected static string $resource = GlpiMigrationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
