<?php

namespace App\Filament\Resources\GlpiMigrationLogs;

use App\Filament\Resources\GlpiMigrationLogs\Pages\ListGlpiMigrationLogs;
use App\Filament\Resources\GlpiMigrationLogs\Pages\ViewGlpiMigrationLog;
use App\Filament\Resources\GlpiMigrationLogs\Schemas\GlpiMigrationLogForm;
use App\Filament\Resources\GlpiMigrationLogs\Tables\GlpiMigrationLogsTable;
use App\Models\GlpiMigrationLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class GlpiMigrationLogResource extends Resource
{
    protected static ?string $model = GlpiMigrationLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Logs GLPI';

    protected static ?string $modelLabel = 'Log GLPI';

    protected static ?string $pluralModelLabel = 'Logs GLPI';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?int $navigationSort = 91;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return GlpiMigrationLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlpiMigrationLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGlpiMigrationLogs::route('/'),
            'view' => ViewGlpiMigrationLog::route('/{record}'),
        ];
    }
}
