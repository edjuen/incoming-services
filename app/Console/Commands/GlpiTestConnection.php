<?php

namespace App\Console\Commands;

use App\Services\Glpi\GlpiClient;
use Illuminate\Console\Command;
use Throwable;

class GlpiTestConnection extends Command
{
    protected $signature = 'glpi:test';

    protected $description = 'Prueba la conexión con la integración GLPI activa';

    public function handle(): int
    {
        $this->info('Probando conexión con GLPI...');

        try {
            $client = GlpiClient::fromActiveIntegration();

            $session = $client->testConnection();

            $this->info('Conexión correcta con GLPI.');
            $this->line('Integración: ' . $client->integration()->name);
            $this->line('URL API: ' . $client->integration()->apiBaseUrl());

            $glpiVersion = data_get($session, 'session.glpiversion')
                ?: data_get($session, 'glpiversion')
                ?: 'No detectada';

            $this->line('Versión GLPI: ' . $glpiVersion);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Error conectando con GLPI:');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}