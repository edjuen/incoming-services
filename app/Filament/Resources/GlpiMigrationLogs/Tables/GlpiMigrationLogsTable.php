<?php

namespace App\Filament\Resources\GlpiMigrationLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GlpiMigrationLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('service_id')
                    ->label('Servicio')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('glpi_ticket_id')
                    ->label('Ticket GLPI')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('integration.name')
                    ->label('Integración')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'error' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(60)
                    ->toggleable()
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'success' => 'Correcto',
                        'error' => 'Error',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),
            ])
            ->defaultSort('id', 'desc');
    }
}