<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServicesSlaStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $avgAssign = Service::whereNotNull('assigned_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, assigned_at)) as avg_minutes')
            ->value('avg_minutes');

        $avgAccept = Service::whereNotNull('accepted_at')
            ->whereNotNull('assigned_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, assigned_at, accepted_at)) as avg_minutes')
            ->value('avg_minutes');

        $avgArrival = Service::whereNotNull('on_scene_at')
            ->whereNotNull('on_route_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, on_route_at, on_scene_at)) as avg_minutes')
            ->value('avg_minutes');

        $avgTotal = Service::whereNotNull('completed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as avg_minutes')
            ->value('avg_minutes');

        return [
            Stat::make('Prom. asignación', round($avgAssign ?? 0) . ' min')
                ->description('Desde creación hasta asignación')
                ->color('info'),

            Stat::make('Prom. aceptación', round($avgAccept ?? 0) . ' min')
                ->description('Desde asignación hasta aceptación')
                ->color('primary'),

            Stat::make('Prom. llegada', round($avgArrival ?? 0) . ' min')
                ->description('Desde en camino hasta en sitio')
                ->color('warning'),

            Stat::make('Prom. total', round($avgTotal ?? 0) . ' min')
                ->description('Desde creación hasta finalización')
                ->color('success'),
        ];
    }
}
