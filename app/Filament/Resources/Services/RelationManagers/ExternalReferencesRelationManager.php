<?php

namespace App\Filament\Resources\Services\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExternalReferencesRelationManager extends RelationManager
{
    protected static string $relationship = 'externalReferences';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('provider_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('provider_name')
            ->columns([
                TextColumn::make('provider_name')
                    ->searchable()
		    ->label('Proveedor externo'),
		TextColumn::make('provider_name')
		    ->label('Proveedor externo'),
		
		TextColumn::make('external_case_number')
		    ->label('Expediente'),

		TextColumn::make('external_service_id')
		    ->label('ID Servicio'),

		TextColumn::make('external_provider_service_id')
		    ->label('ID Servicio Proveedor'),

		TextColumn::make('external_status')
		    ->label('Estado externo')
		    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
