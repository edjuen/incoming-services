<?php

namespace App\Filament\Resources\GlpiIntegrations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GlpiIntegrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Conexión GLPI')
                    ->description('Configura la conexión API hacia GLPI. Los tokens se guardan cifrados en la base de datos.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->default('GLPI Principal'),

                        TextInput::make('base_url')
                            ->label('URL GLPI')
                            ->helperText('Ejemplo: https://tuglpiserver.com o https://tuglpiserver.com/apirest.php')
                            ->required()
                            ->url()
                            ->maxLength(255),

                        TextInput::make('app_token')
                            ->label('App Token')
                            ->password()
                            ->revealable()
                            ->helperText('Se guardará cifrado. En edición, déjalo vacío si no quieres cambiarlo.')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->formatStateUsing(fn () => null)
                            ->dehydrated(fn (?string $state): bool => filled($state)),

                        TextInput::make('user_token')
                            ->label('User Token del usuario técnico GLPI')
                            ->password()
                            ->revealable()
                            ->helperText('Token del usuario técnico que usará Laravel para conectarse a GLPI. En edición, déjalo vacío si no quieres cambiarlo.')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->formatStateUsing(fn () => null)
                            ->dehydrated(fn (?string $state): bool => filled($state)),

                        TextInput::make('default_entity_id')
                            ->label('Entidad GLPI por defecto')
                            ->numeric()
                            ->required()
                            ->default(0),

                        Toggle::make('verify_ssl')
                            ->label('Verificar certificado SSL')
                            ->helperText('Déjalo activo si GLPI usa certificado válido.')
                            ->default(true),

                        Toggle::make('active')
                            ->label('Integración activa')
                            ->helperText('Solo debe existir una integración activa.')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
