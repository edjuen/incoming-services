<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('provider_id')
                    ->label('Proveedor')
                    ->relationship('provider', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('unit_key')
                    ->label('Clave Unidad'),

                TextInput::make('brand')
                    ->label('Marca'),

                TextInput::make('model')
                    ->label('Modelo'),

                TextInput::make('year')
                    ->label('Año'),

                TextInput::make('color')
                    ->label('Color'),

                TextInput::make('plates')
                    ->label('Placas'),

                TextInput::make('vin')
                    ->label('VIN / Chasis'),

                TextInput::make('unit_type')
                    ->label('Tipo de Unidad')
                    ->helperText('Grúa plataforma, grúa arrastre, moto, etc.'),

                Toggle::make('is_active')
                    ->label('Activa')
                    ->default(true),

            ]);
    }
}
