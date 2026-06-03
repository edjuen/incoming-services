<?php

namespace App\Filament\Resources\Operators\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OperatorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('provider.name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('operator_key')
                    ->label('Clave')
                    ->searchable(),

                TextColumn::make('first_name')
                    ->label('Nombre')
                    ->searchable(),

                TextColumn::make('last_name')
                    ->label('Apellido Paterno')
                    ->searchable(),

                TextColumn::make('second_last_name')
                    ->label('Apellido Materno'),

                TextColumn::make('phone')
                    ->label('Teléfono'),

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
