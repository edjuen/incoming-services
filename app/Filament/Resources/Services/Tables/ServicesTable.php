<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Services\AxaService;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
	    ->poll('10s')
	    ->defaultSort('created_at', 'desc')
            ->columns([
		TextColumn::make('status')
		    ->label('Estado')
		    ->badge()
		    ->color(fn (string $state): string => match ($state) {
		        'new' => 'danger',
		        'pending' => 'warning',
		        'assigned' => 'info',
		        'accepted' => 'primary',
		        'on_route' => 'warning',
		        'on_scene' => 'success',
		        'completed' => 'success',
		        'cancelled' => 'danger',
		        default => 'gray',
		    }),
                TextColumn::make('folio')
                    ->searchable(),
                TextColumn::make('source')
                    ->searchable(),
                TextColumn::make('insuranceCompany.name')
		    ->label('Aseguradora')
		    ->searchable()
		    ->sortable(),
		TextColumn::make('integrationProvider.name')
		    ->label('Integración')
		    ->sortable()
		    ->searchable(),
		TextColumn::make('serviceType.name')
		    ->label('Tipo de servicio')
		    ->searchable()
		    ->sortable(),
		TextColumn::make('provider.name')
		    ->label('Proveedor')
		    ->searchable()
		    ->sortable(),
                TextColumn::make('insured_name')
                    ->searchable(),
                TextColumn::make('insured_phone')
                    ->searchable(),
                TextColumn::make('origin_coordinates')
                    ->searchable(),
                TextColumn::make('destination_coordinates')
                    ->searchable(),
                TextColumn::make('vehicle')
                    ->searchable(),
		TextColumn::make('operator_full_name')
		    ->label('Operador')
		    ->getStateUsing(fn ($record) => $record->operator
		        ? trim($record->operator->first_name . ' ' . $record->operator->last_name . ' ' . $record->operator->second_last_name)
		        : null
		    )
		    ->searchable(['operators.first_name', 'operators.last_name', 'operators.second_last_name']),

		TextColumn::make('unit_label')
		    ->label('Unidad')
		    ->getStateUsing(fn ($record) => $record->unit
		        ? trim($record->unit->unit_key . ' - ' . $record->unit->brand . ' ' . $record->unit->model . ' / ' . $record->unit->plates)
		        : null
		    ),

		TextColumn::make('estimated_arrival_minutes')
		    ->label('ETA'),
                

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
	    SelectFilter::make('status')
	        ->label('Estado')
	        ->options([
	            'new' => 'Nuevo',
	            'pending' => 'Pendiente',
	            'assigned' => 'Asignado',
	            'accepted' => 'Aceptado',
	            'on_route' => 'En camino',
        	    'on_scene' => 'En sitio',
	            'completed' => 'Finalizado',
	            'cancelled' => 'Cancelado',
        	]),

	    SelectFilter::make('insurance_company_id')
	        ->label('Aseguradora')
        	->relationship('insuranceCompany', 'name')
	        ->searchable()
        	->preload(),

	    SelectFilter::make('service_type_id')
	        ->label('Tipo de servicio')
        	->relationship('serviceType', 'name')
	        ->searchable()
        	->preload(),

	    SelectFilter::make('provider_id')
	        ->label('Proveedor')
	        ->relationship('provider', 'name')
        	->searchable()
        	->preload(),
	])
            ->recordActions([
	    Action::make('assign')
	    	->label('Asignar')
	    	->icon('heroicon-o-user-plus')
	    	->color('info')
	    	->visible(fn ($record) => in_array($record->status, ['new', 'pending']))
	    	->schema([
	    	    Select::make('provider_id')
        	    ->label('Proveedor')
	            ->relationship('provider', 'name')
        	    ->searchable()
	            ->preload()
        	    ->required(),
		Select::make('operator_id')
		    ->label('Operador')
		    ->relationship('operator', 'first_name')
		    ->getOptionLabelFromRecordUsing(fn ($record) =>
		        trim($record->first_name . ' ' . $record->last_name . ' ' . $record->second_last_name)
		    )
		    ->searchable()
		    ->preload()
		    ->required(),

		Select::make('unit_id')
		    ->label('Unidad')
		    ->relationship('unit', 'unit_key')
		    ->getOptionLabelFromRecordUsing(fn ($record) =>
		        trim($record->unit_key . ' - ' . $record->brand . ' ' . $record->model . ' / ' . $record->plates)
		    )
		    ->searchable()
		    ->preload()
		    ->required(),

	        TextInput::make('estimated_arrival_minutes')
	            ->label('Minutos estimados de arribo')
	            ->numeric()
	            ->required(),
	    ])
	    ->action(function ($record, array $data) {
	        $record->update([
	            'provider_id' => $data['provider_id'],
		    'operator_id' => $data['operator_id'],
        	    'unit_id' => $data['unit_id'],
	            'estimated_arrival_minutes' => $data['estimated_arrival_minutes'],
	            'status' => 'assigned',
	        ]);
	    }),

	    Action::make('accept')
	       	->label('Aceptar')
	        ->icon('heroicon-o-check-circle')
	       	->color('primary')
	        ->visible(fn ($record) => $record->status === 'assigned')
	       	->requiresConfirmation()
		->action(function ($record) {
	        try {
	            if ($record->externalReferences()
	                ->where('provider_name', 'AXA')
	                ->exists()) {
	                app(AxaService::class)->acceptService($record);
	            }

	            $record->update([
	                'status' => 'accepted',
	            ]);
	        } catch (\Throwable $e) {
	            $record->events()->create([
	                'event_type' => 'integration_error',
	                'title' => 'Error al enviar aceptación a AXA',
	                'description' => $e->getMessage(),
	                'old_status' => $record->status,
	                'new_status' => $record->status,
	            ]);

	            throw $e;
	        }
	    }),

	    Action::make('on_route')
        	->label('En camino')
	        ->icon('heroicon-o-truck')
        	->color('warning')
	        ->visible(fn ($record) => $record->status === 'accepted')
        	->action(fn ($record) => $record->update(['status' => 'on_route'])),

	Action::make('on_scene')
	    ->label('En sitio')
	    ->icon('heroicon-o-map-pin')
	    ->color('success')
	    ->visible(fn ($record) => $record->status === 'on_route')
	    ->requiresConfirmation()
	    ->action(function ($record) {
	        try {
	            if ($record->externalReferences()
	                ->where('provider_name', 'AXA')
	                ->exists()) {
	                app(AxaService::class)->contactService($record);
	            }

	            $record->update([
	                'status' => 'on_scene',
	            ]);
	        } catch (\Throwable $e) {
	            $record->events()->create([
	                'event_type' => 'integration_error',
	                'title' => 'Error al enviar contacto a AXA',
	                'description' => $e->getMessage(),
	                'old_status' => $record->status,
	                'new_status' => $record->status,
	            ]);

	            throw $e;
	        }
	    }),

	Action::make('complete')
	    ->label('Finalizar')
	    ->icon('heroicon-o-flag')
	    ->color('success')
	    ->visible(fn ($record) => $record->status === 'on_scene')
	    ->requiresConfirmation()
	    ->action(function ($record) {
	        try {
	            if ($record->externalReferences()
	                ->where('provider_name', 'AXA')
	                ->exists()) {
	                app(AxaService::class)->finishService($record);
	            }

	            $record->update([
	                'status' => 'completed',
	            ]);
	        } catch (\Throwable $e) {
	            $record->events()->create([
	                'event_type' => 'integration_error',
	                'title' => 'Error al enviar finalización a AXA',
	                'description' => $e->getMessage(),
	                'old_status' => $record->status,
	                'new_status' => $record->status,
	            ]);

	            throw $e;
	        }
	    }),
	
	Action::make('cancel')
	    ->label('Cancelar')
	    ->icon('heroicon-o-x-circle')
	    ->color('danger')
	    ->visible(fn ($record) => ! in_array($record->status, ['completed', 'cancelled']))
	    ->requiresConfirmation()
	    ->schema([
	        Select::make('reject_code')
	            ->label('Motivo de cancelación / rechazo')
	            ->options([
	                '08' => '08 - Proveedor sin grúas disponibles',
	                '09' => '09 - No cubrimos la zona',
	                '14' => '14 - Error de cabina / mala tipificación',
	                '15' => '15 - Proveedor cancela',
	                '16' => '16 - Error de cabina / mala tipificación',
	                '27' => '27 - Tiempo de arribo mayor al permitido',
	            ])
	            ->required(),
	    ])
	    ->action(function ($record, array $data) {
	        try {
	            if ($record->externalReferences()
	                ->where('provider_name', 'AXA')
	                ->exists()) {
	                app(AxaService::class)->cancelService($record, $data['reject_code']);
	            }

	            $record->update([
	                'status' => 'cancelled',
	            ]);
	        } catch (\Throwable $e) {
	            $record->events()->create([
	                'event_type' => 'integration_error',
	                'title' => 'Error al enviar cancelación a AXA',
	                'description' => $e->getMessage(),
	                'old_status' => $record->status,
	                'new_status' => $record->status,
	            ]);

	            throw $e;
	        }
	    }),

	    EditAction::make(),
	])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
