<?php

namespace App\Filament\Resources\InsuranceCompanies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class InsuranceCompaniesTable
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

    IconColumn::make('api_enabled')
        ->label('API')
        ->boolean(),

    IconColumn::make('is_active')
        ->label('Activa')
        ->boolean(),

    TextColumn::make('created_at')
        ->label('Creada')
        ->dateTime()
        ->sortable(),
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
