<?php

namespace App\Filament\Resources\IntegrationProviders;

use App\Filament\Resources\IntegrationProviders\Pages\CreateIntegrationProvider;
use App\Filament\Resources\IntegrationProviders\Pages\EditIntegrationProvider;
use App\Filament\Resources\IntegrationProviders\Pages\ListIntegrationProviders;
use App\Filament\Resources\IntegrationProviders\Schemas\IntegrationProviderForm;
use App\Filament\Resources\IntegrationProviders\Tables\IntegrationProvidersTable;
use App\Models\IntegrationProvider;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IntegrationProviderResource extends Resource
{
    protected static ?string $model = IntegrationProvider::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Integration';

    public static function form(Schema $schema): Schema
    {
        return IntegrationProviderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IntegrationProvidersTable::configure($table);
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
            'index' => ListIntegrationProviders::route('/'),
            'create' => CreateIntegrationProvider::route('/create'),
            'edit' => EditIntegrationProvider::route('/{record}/edit'),
        ];
    }
}
