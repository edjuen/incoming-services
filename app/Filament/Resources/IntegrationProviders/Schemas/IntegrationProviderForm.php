<?php

namespace App\Filament\Resources\IntegrationProviders\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class IntegrationProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
		Select::make('insurance_company_id')
		    ->label('Aseguradora')
		    ->relationship('insuranceCompany', 'name')
		    ->searchable()
		    ->preload()
		    ->nullable(),
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->maxLength(255),

		Select::make('driver')
		    ->label('Driver')
		    ->options([
		        'axa' => 'AXA',
		        'gnp' => 'GNP',
		        'qualitas' => 'Qualitas',
		        'glpi' => 'GLPI',
		        'manual' => 'Manual',
		    ])
		    ->required(),

                TextInput::make('base_url')
                    ->label('URL Base')
                    ->url()
                    ->maxLength(255),

                TextInput::make('public_key')
                    ->label('Public Key / API Key')
                    ->maxLength(255),

                TextInput::make('secret_key')
                    ->label('Secret Key')
                    ->password()
                    ->revealable(),

                TextInput::make('username')
                    ->label('Usuario')
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable(),

                Textarea::make('settings')
                    ->label('Settings JSON')
                    ->helperText('Ejemplo: {"rfc":"ABC123456","polling_interval":60}')
                    ->rows(5),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}