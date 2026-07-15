<?php

namespace App\Filament\Resources\GlpiMigrationLogs\Pages;

use App\Filament\Resources\GlpiMigrationLogs\GlpiMigrationLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGlpiMigrationLog extends CreateRecord
{
    protected static string $resource = GlpiMigrationLogResource::class;
}
