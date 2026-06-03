<?php

namespace App\Filament\Resources\InsuranceCompanies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InsuranceCompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('code')
                    ->label('Código')
                    ->maxLength(255),

                Toggle::make('api_enabled')
                    ->label('API activa')
                    ->default(false),

                Toggle::make('is_active')
                    ->label('Activa')
                    ->default(true),
            ]);
    }
}
