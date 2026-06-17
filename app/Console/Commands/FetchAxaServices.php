<?php

namespace App\Console\Commands;

use App\Jobs\FetchAxaServicesJob;
use Illuminate\Console\Command;

class FetchAxaServices extends Command
{
    protected $signature = 'axa:fetch-services';

    protected $description = 'Consulta servicios nuevos desde AXA';

    public function handle(): int
    {
    	$integrations = \App\Models\IntegrationProvider::query()
    	    ->where('is_active', true)
    	    ->where('code', 'like', 'AXA%')
    	    ->get();

    	foreach ($integrations as $integration) {
    	    FetchAxaServicesJob::dispatchSync($integration->id);
    	}

    	$this->info('Consulta AXA ejecutada para ' . $integrations->count() . ' integraciones activas.');

    	return self::SUCCESS;
    }
}