<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\User;
use App\Services\Glpi\MigrateServiceToGlpi;
use Illuminate\Console\Command;
use Throwable;

class GlpiMigrateService extends Command
{
    protected $signature = 'glpi:migrate-service 
        {service_id : ID del servicio Laravel}
        {--user_id= : ID del usuario Laravel que hará la migración}';

    protected $description = 'Migra un servicio Laravel hacia GLPI creando un ticket en estado En proceso';

    public function handle(MigrateServiceToGlpi $migrator): int
    {
        $serviceId = (int) $this->argument('service_id');
        $userId = $this->option('user_id');

        $service = Service::query()
            ->with([
                'insuranceCompany',
                'integrationProvider',
                'serviceType',
                'provider',
                'operator',
                'unit',
            ])
            ->find($serviceId);

        if (! $service) {
            $this->error('No existe el servicio con ID ' . $serviceId . '.');
            return self::FAILURE;
        }

        $user = $this->resolveUser($userId);

        if (! $user) {
            $this->error('No se encontró un usuario válido para ejecutar la migración.');
            return self::FAILURE;
        }

        $this->info('Migrando servicio a GLPI...');
        $this->line('Servicio Laravel ID: ' . $service->id);
        $this->line('Folio: ' . ($service->folio ?? 'Sin folio'));
        $this->line('Estado Laravel: ' . $service->status);
        $this->line('Usuario Laravel: ' . $user->name . ' / GLPI ID: ' . ($user->glpi_user_id ?: 'SIN CONFIGURAR'));

        try {
            $ticketId = $migrator->handle($service, $user);

            $this->info('Servicio migrado correctamente.');
            $this->line('Ticket GLPI creado: #' . $ticketId);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Error migrando servicio a GLPI:');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    protected function resolveUser(?string $userId): ?User
    {
        if ($userId) {
            return User::query()->find((int) $userId);
        }

        return User::query()
            ->whereNotNull('glpi_user_id')
            ->orderBy('id')
            ->first();
    }
}
