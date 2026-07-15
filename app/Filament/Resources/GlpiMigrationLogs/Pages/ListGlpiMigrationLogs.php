<?php

namespace App\Filament\Resources\GlpiMigrationLogs\Pages;

use App\Filament\Resources\GlpiMigrationLogs\GlpiMigrationLogResource;
use Filament\Resources\Pages\ListRecords;

class ListGlpiMigrationLogs extends ListRecords
{
    protected static string $resource = GlpiMigrationLogResource::class;
}