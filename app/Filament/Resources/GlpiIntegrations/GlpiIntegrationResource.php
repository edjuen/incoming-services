<?php

namespace App\Filament\Resources\GlpiIntegrations;

use App\Filament\Resources\GlpiIntegrations\Pages\CreateGlpiIntegration;
use App\Filament\Resources\GlpiIntegrations\Pages\EditGlpiIntegration;
use App\Filament\Resources\GlpiIntegrations\Pages\ListGlpiIntegrations;
use App\Filament\Resources\GlpiIntegrations\Schemas\GlpiIntegrationForm;
use App\Filament\Resources\GlpiIntegrations\Tables\GlpiIntegrationsTable;
use App\Models\GlpiIntegration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GlpiIntegrationResource extends Resource
{
    protected static ?string $model = GlpiIntegration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Integración GLPI';

    protected static ?string $modelLabel = 'Integración GLPI';

    protected static ?string $pluralModelLabel = 'Integraciones GLPI';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 90;

    public static function form(Schema $schema): Schema
    {
        return GlpiIntegrationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GlpiIntegrationsTable::configure($table);
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
            'index' => ListGlpiIntegrations::route('/'),
            'create' => CreateGlpiIntegration::route('/create'),
            'edit' => EditGlpiIntegration::route('/{record}/edit'),
        ];
    }
}
