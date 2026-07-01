<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Operator;
use App\Models\Unit;
use App\Services\AxaService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

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

                TextColumn::make('axa_acceptance_timer')
                    ->label('Tiempo AXA')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if (in_array($record->status, ['accepted', 'on_route', 'on_scene', 'completed'])) {
                            return 'Aceptado';
                        }

                        if ($record->status === 'cancelled') {
                            return 'Cancelado';
                        }

                        $deadline = Carbon::parse($record->created_at)->addMinutes(2);
                        $secondsLeft = (int) now()->diffInSeconds($deadline, false);

                        if ($secondsLeft <= 0) {
                            return 'Vencido';
                        }

                        $minutes = floor($secondsLeft / 60);
                        $seconds = $secondsLeft % 60;

                        return sprintf('%02d:%02d', $minutes, $seconds);
                    })
                    ->color(function ($record): string {
                        if (in_array($record->status, ['accepted', 'on_route', 'on_scene', 'completed'])) {
                            return 'success';
                        }

                        if ($record->status === 'cancelled') {
                            return 'gray';
                        }

                        $deadline = Carbon::parse($record->created_at)->addMinutes(2);
                        $secondsLeft = (int) now()->diffInSeconds($deadline, false);

                        if ($secondsLeft <= 0) {
                            return 'danger';
                        }

                        if ($secondsLeft <= 60) {
                            return 'warning';
                        }

                        return 'success';
                    }),

                TextColumn::make('folio')
                    ->label('Folio')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('source')
                    ->label('Origen')
                    ->searchable()
		    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('integrationProvider.name')
                    ->label('Integración')
                    ->sortable()
                    ->searchable()
		    ->toggleable(),

                TextColumn::make('serviceType.name')
                    ->label('Tipo de servicio')
                    ->searchable()
                    ->sortable()
		    ->toggleable(),

                TextColumn::make('insured_name')
                    ->label('Asegurado')
                    ->searchable()
                    ->limit(30)
		    ->toggleable(),

                TextColumn::make('vehicle')
                    ->label('Vehículo')
                    ->searchable()
                    ->limit(35)
		    ->toggleable(),

                TextColumn::make('operator_full_name')
                    ->label('Operador')
                    ->getStateUsing(fn ($record) => $record->operator
                        ? trim($record->operator->first_name . ' ' . $record->operator->last_name . ' ' . $record->operator->second_last_name)
                        : null
                    )
                    ->placeholder('Sin operador')
		    ->toggleable(),

                TextColumn::make('unit_label')
                    ->label('Unidad')
                    ->getStateUsing(fn ($record) => $record->unit
                        ? trim($record->unit->unit_key . ' - ' . $record->unit->brand . ' ' . $record->unit->model . ' / ' . $record->unit->plates)
                        : null
                    )
                    ->placeholder('Sin unidad')
		    ->toggleable(),

                TextColumn::make('estimated_arrival_minutes')
                    ->label('ETA')
                    ->suffix(' min')
                    ->sortable()
                    ->placeholder('N/A')
		    ->toggleable(),

		TextColumn::make('insured_phone')
		    ->label('Teléfono asegurado')
		    ->searchable()
		    ->toggleable(isToggledHiddenByDefault: true),

		TextColumn::make('origin_coordinates')
		    ->label('Coord. origen')
		    ->searchable()
		    ->toggleable(isToggledHiddenByDefault: true),

		TextColumn::make('destination_coordinates')
		    ->label('Coord. destino')
		    ->searchable()
		    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Recibido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('assigned_at')
                    ->label('Asignado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Sin asignar')
		    ->toggleable(),

                TextColumn::make('eta_deadline')
                    ->label('Hora límite')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if (! $record->estimated_arrival_minutes) {
                            return null;
                        }

                        $baseTime = $record->assigned_at ?? $record->created_at;

                        return Carbon::parse($baseTime)
                            ->addMinutes((int) $record->estimated_arrival_minutes)
                            ->format('H:i');
                    })
                    ->color(function ($record): string {
                        if (! $record->estimated_arrival_minutes) {
                            return 'gray';
                        }

                        if (in_array($record->status, ['completed', 'cancelled'])) {
                            return 'gray';
                        }

                        $baseTime = $record->assigned_at ?? $record->created_at;

                        $deadline = Carbon::parse($baseTime)
                            ->addMinutes((int) $record->estimated_arrival_minutes);

                        if (now()->greaterThan($deadline)) {
                            return 'danger';
                        }

                        if (now()->diffInMinutes($deadline, false) <= 10) {
                            return 'warning';
                        }

                        return 'success';
                    }),
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
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('operator_id', null);
                                $set('unit_id', null);
                            })
                            ->required(),

                        Select::make('operator_id')
                            ->label('Operador')
                            ->options(function (Get $get) {
                                $providerId = $get('provider_id');

                                if (! $providerId) {
                                    return [];
                                }

                                return Operator::query()
                                    ->where('provider_id', $providerId)
                                    ->orderBy('first_name')
                                    ->get()
                                    ->mapWithKeys(fn ($operator) => [
                                        $operator->id => trim(
                                            $operator->first_name . ' ' .
                                            $operator->last_name . ' ' .
                                            $operator->second_last_name
                                        ),
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Get $get) => ! $get('provider_id'))
                            ->required(),

                        Select::make('unit_id')
                            ->label('Unidad')
                            ->options(function (Get $get) {
                                $providerId = $get('provider_id');

                                if (! $providerId) {
                                    return [];
                                }

                                return Unit::query()
                                    ->where('provider_id', $providerId)
                                    ->orderBy('unit_key')
                                    ->get()
                                    ->mapWithKeys(fn ($unit) => [
                                        $unit->id => trim(
                                            $unit->unit_key . ' - ' .
                                            $unit->brand . ' ' .
                                            $unit->model . ' / ' .
                                            $unit->plates
                                        ),
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Get $get) => ! $get('provider_id'))
                            ->required(),

                        TextInput::make('estimated_arrival_minutes')
                            ->label('Minutos estimados de arribo')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $operatorIsValid = Operator::query()
                            ->where('id', $data['operator_id'])
                            ->where('provider_id', $data['provider_id'])
                            ->exists();

                        $unitIsValid = Unit::query()
                            ->where('id', $data['unit_id'])
                            ->where('provider_id', $data['provider_id'])
                            ->exists();

                        if (! $operatorIsValid) {
                            throw ValidationException::withMessages([
                                'operator_id' => 'El operador seleccionado no pertenece al proveedor.',
                            ]);
                        }

                        if (! $unitIsValid) {
                            throw ValidationException::withMessages([
                                'unit_id' => 'La unidad seleccionada no pertenece al proveedor.',
                            ]);
                        }

                        $record->update([
                            'provider_id' => $data['provider_id'],
                            'operator_id' => $data['operator_id'],
                            'unit_id' => $data['unit_id'],
                            'estimated_arrival_minutes' => $data['estimated_arrival_minutes'],
                            'assigned_at' => now(),
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
                                'accepted_at' => now(),
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
                    ->action(fn ($record) => $record->update([
                        'on_route_at' => now(),
                        'status' => 'on_route',
                    ])),

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
                                'on_scene_at' => now(),
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
                                'completed_at' => now(),
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
                                'cancelled_at' => now(),
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