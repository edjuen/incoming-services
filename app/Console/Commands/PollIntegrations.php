<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Jobs\FetchAxaServicesJob;
use App\Models\IntegrationProvider;

#[Signature('app:poll-integrations')]
#[Description('Command description')]
class PollIntegrations extends Command
{
    protected $signature = 'app:poll-integrations';

    protected $description = 'Consulta integraciones activas según su driver';

    public function handle(): int
    {
        $integrations = IntegrationProvider::query()
            ->where('is_active', true)
            ->whereNotNull('driver')
            ->get();

        foreach ($integrations as $integration) {
            match ($integration->driver) {
                'axa' => FetchAxaServicesJob::dispatchSync($integration->id),
                default => logger()->warning('Driver no soportado', [
                    'integration_id' => $integration->id,
                    'driver' => $integration->driver,
                    'code' => $integration->code,
                ]),
            };
        }

        $this->info('Integraciones consultadas: ' . $integrations->count());

        return self::SUCCESS;
    }
}