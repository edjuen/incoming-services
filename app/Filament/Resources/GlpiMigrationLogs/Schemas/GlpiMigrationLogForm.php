<?php

namespace App\Filament\Resources\GlpiMigrationLogs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GlpiMigrationLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información')
                    ->schema([
                        TextInput::make('service_id')
                            ->label('Servicio Laravel')
                            ->disabled(),

                        TextInput::make('glpi_ticket_id')
                            ->label('Ticket GLPI')
                            ->disabled(),

                        TextInput::make('status')
                            ->label('Estado')
                            ->disabled(),

                        Textarea::make('error_message')
                            ->label('Error')
                            ->rows(5)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Payload enviado')
                    ->schema([
                        Textarea::make('request_payload')
                            ->label('Request')
                            ->formatStateUsing(function ($state): string {
                                if (blank($state)) {
                                    return '';
                                }

                                if (is_string($state)) {
                                    $decoded = json_decode($state, true);

                                    if (json_last_error() === JSON_ERROR_NONE) {
                                        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                    }

                                    return $state;
                                }

                                return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            })
                            ->rows(12)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Section::make('Respuesta GLPI')
                    ->schema([
                        Textarea::make('response_payload')
                            ->label('Response')
                            ->formatStateUsing(function ($state): string {
                                if (blank($state)) {
                                    return '';
                                }

                                if (is_string($state)) {
                                    $decoded = json_decode($state, true);

                                    if (json_last_error() === JSON_ERROR_NONE) {
                                        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                    }

                                    return $state;
                                }

                                return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            })
                            ->rows(12)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
