<?php

namespace App\Filament\Resources\ServiceTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class ServiceTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
    TextColumn::make('name')
        ->label('Nombre')
        ->searchable()
        ->sortable(),

    TextColumn::make('code')
        ->label('Código')
        ->searchable()
        ->sortable(),

    IconColumn::make('is_active')
        ->label('Activo')
        ->boolean(),
])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
