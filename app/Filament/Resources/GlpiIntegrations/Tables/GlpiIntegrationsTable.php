<?php

namespace App\Filament\Resources\GlpiIntegrations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class GlpiIntegrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('active')
                    ->label('Activa')
                    ->boolean(),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('base_url')
                    ->label('URL')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('default_entity_id')
                    ->label('Entidad')
                    ->sortable(),

                IconColumn::make('verify_ssl')
                    ->label('SSL')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label('Activa'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionadas'),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
