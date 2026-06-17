<?php

namespace App\Filament\Resources\IntegrationProviders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Jobs\FetchAxaServicesJob;

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
		Action::make('fetch_now')
		    ->label('Consultar ahora')
		    ->icon('heroicon-o-arrow-path')
		    ->color('info')
		    ->visible(fn ($record) => $record->is_active && str_starts_with($record->code, 'AXA'))
		    ->action(function ($record) {
	        	FetchAxaServicesJob::dispatchSync($record->id);
		        \Filament\Notifications\Notification::make()
	        	    ->title('Consulta ejecutada')
		            ->body('Se consultó la integración ' . $record->name)
		            ->success()
		            ->send();
	        }),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
