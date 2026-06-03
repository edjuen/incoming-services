<?php

namespace App\Filament\Resources\IntegrationLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class IntegrationLogsTable
{
    public static function configure(Table $table): Table
	{
	return $table
	    ->columns([
	    TextColumn::make('created_at')
	        ->label('Fecha')
	        ->dateTime()
	        ->sortable(),
	    TextColumn::make('action')
	        ->label('Acción')
	        ->searchable(),
	    TextColumn::make('endpoint')
	        ->label('Endpoint')
	        ->limit(40),
	    TextColumn::make('status_code')
	        ->label('HTTP'),
	    IconColumn::make('success')
	        ->label('OK')
	        ->boolean(),
	    TextColumn::make('error_message')
	        ->label('Error')
	        ->limit(50),
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
