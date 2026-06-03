<?php

namespace App\Filament\Resources\IntegrationLogs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class IntegrationLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('direction')->label('Dirección'),
                TextInput::make('action')->label('Acción'),
                TextInput::make('endpoint')->label('Endpoint'),
                TextInput::make('status_code')->label('Status Code')->numeric(),
                Toggle::make('success')->label('Éxito'),

                Textarea::make('request_payload')
                    ->label('Request')
                    ->rows(6),

                Textarea::make('response_payload')
                    ->label('Response')
                    ->rows(6),

                Textarea::make('error_message')
                    ->label('Error')
                    ->rows(4),
            ]);
    }
}
