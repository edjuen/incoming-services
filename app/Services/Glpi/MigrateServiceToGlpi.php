<?php

namespace App\Services\Glpi;

use App\Models\GlpiIntegration;
use App\Models\GlpiMigrationLog;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class MigrateServiceToGlpi
{
    public function __construct(
        protected GlpiPayloadBuilder $payloadBuilder
    ) {}

    public function handle(Service $service, User $user): int
    {
        return DB::transaction(function () use ($service, $user) {
            $service->refresh();

            $this->validateService($service, $user);

            $integration = GlpiIntegration::activeIntegration();

            $client = new GlpiClient($integration);

            $ticketInput = $this->payloadBuilder->buildTicketInput(
                service: $service,
                user: $user,
                integration: $integration
            );

            $log = GlpiMigrationLog::create([
                'service_id' => $service->id,
                'glpi_integration_id' => $integration->id,
                'user_id' => $user->id,
                'action' => 'migrate_service',
                'status' => 'pending',
                'request_payload' => [
                    'input' => $ticketInput,
                ],
            ]);

            try {
                $response = $client->createTicket($ticketInput);

                $ticketId = (int) $response['id'];

                $service->update([
                    'glpi_ticket_id' => $ticketId,
                    'glpi_migrated_at' => now(),
                    'glpi_migrated_by' => $user->id,
                    'glpi_migration_error' => null,
                ]);

                $log->update([
                    'glpi_ticket_id' => $ticketId,
                    'status' => 'success',
                    'response_payload' => $response,
                    'error_message' => null,
                ]);

                return $ticketId;
            } catch (Throwable $e) {
                $service->update([
                    'glpi_migration_error' => $e->getMessage(),
                ]);

                $log->update([
                    'status' => 'error',
                    'error_message' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    protected function validateService(Service $service, User $user): void
    {
        if ($service->glpi_ticket_id) {
            throw new RuntimeException('Este servicio ya fue migrado a GLPI con ticket #' . $service->glpi_ticket_id . '.');
        }

        if (! $service->canBeMigratedToGlpi()) {
            throw new RuntimeException('Este servicio no está en un estado permitido para migrar a GLPI.');
        }

        if (! $user->glpi_user_id) {
            throw new RuntimeException('Tu usuario Laravel no tiene configurado glpi_user_id.');
        }

        if (in_array($service->status, ['new', 'assigned'], true)) {
            throw new RuntimeException('Los servicios en estado new o assigned no pueden enviarse a GLPI.');
        }
    }
}