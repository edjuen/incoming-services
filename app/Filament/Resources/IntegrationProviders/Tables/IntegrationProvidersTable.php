<?php

namespace App\Filament\Resources\IntegrationProviders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class IntegrationProvidersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
	  	TextColumn::make('insuranceCompany.name')
	            ->label('Aseguradora')
	 	    ->searchable()
	    	    ->sortable(),
	    TextColumn::make('name')
        	->label('Nombre')
	        ->searchable()
        	->sortable(),

	    TextColumn::make('code')
        	->label('Código')
	        ->searchable()
        	->sortable(),

	    TextColumn::make('base_url')
        	->label('URL Base')
	        ->limit(40),

	    IconColumn::make('is_active')
	        ->label('Activo')
        	->boolean(),

	    TextColumn::make('created_at')
        	->label('Creado')
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
