<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServicesStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Nuevos', Service::where('status', 'new')->count())
                ->description('Servicios sin asignar')
                ->color('gray'),

            Stat::make('Asignados', Service::where('status', 'assigned')->count())
                ->description('Esperando aceptación')
                ->color('info'),

            Stat::make('Aceptados', Service::where('status', 'accepted')->count())
                ->description('Proveedor aceptó')
                ->color('primary'),

            Stat::make('En camino', Service::where('status', 'on_route')->count())
                ->description('Unidad en ruta')
                ->color('warning'),

            Stat::make('En sitio', Service::where('status', 'on_scene')->count())
                ->description('Unidad llegó')
                ->color('success'),

            Stat::make('Finalizados', Service::where('status', 'completed')->count())
                ->description('Servicios terminados')
                ->color('success'),

            Stat::make('Cancelados', Service::where('status', 'cancelled')->count())
                ->description('Servicios cancelados')
                ->color('danger'),
        ];
    }
}