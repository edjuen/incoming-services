<?php

namespace App\Filament\Resources\Operators\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OperatorForm
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

                TextInput::make('operator_key')
                    ->label('Clave Operador')
                    ->maxLength(100),

                TextInput::make('first_name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->label('Apellido Paterno')
                    ->required()
                    ->maxLength(255),

                TextInput::make('second_last_name')
                    ->label('Apellido Materno')
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(50),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),

            ]);
    }
}
