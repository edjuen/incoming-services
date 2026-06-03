<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('folio'),
                TextInput::make('source'),
                Select::make('insurance_company_id')
    ->relationship('insuranceCompany', 'name')
    ->label('Aseguradora')
    ->searchable()
    ->preload(),

Select::make('service_type_id')
    ->relationship('serviceType', 'name')
    ->label('Tipo de servicio')
    ->searchable()
    ->preload(),
Select::make('provider_id')
    ->relationship('provider', 'name')
    ->label('Proveedor')
    ->searchable()
    ->preload(),
Select::make('operator_id')
    ->label('Operador')
    ->relationship('operator', 'first_name')
    ->getOptionLabelFromRecordUsing(fn ($record) => trim($record->first_name . ' ' . $record->last_name . ' ' . $record->second_last_name))
    ->searchable()
    ->preload(),

Select::make('unit_id')
    ->label('Unidad')
    ->relationship('unit', 'unit_key')
    ->getOptionLabelFromRecordUsing(fn ($record) => trim($record->unit_key . ' - ' . $record->brand . ' ' . $record->model . ' / ' . $record->plates))
    ->searchable()
    ->preload(),
                TextInput::make('insured_name'),
                TextInput::make('insured_phone')
                    ->tel(),
                Textarea::make('origin_address')
                    ->columnSpanFull(),
                TextInput::make('origin_coordinates'),
                Textarea::make('destination_address')
                    ->columnSpanFull(),
                TextInput::make('destination_coordinates'),
                TextInput::make('vehicle'),
		TextInput::make('estimated_arrival_minutes'),
                Select::make('status')
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
    ])
    ->default('new')
    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
