<?php

namespace App\Jobs;

use App\Models\Service;
use App\Models\ServiceExternalReference;
use App\Models\ServiceType;
use App\Services\AxaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Filament\Notifications\Notification;
use App\Models\User;

class FetchAxaServicesJob implements ShouldQueue
{
    use Queueable;

    public function handle(AxaService $axaService): void
    {
        try {
    	    $axaServices = $axaService->getServices();
	} catch (\Throwable $e) {
	    logger()->error('Error al consultar servicios AXA', [
	        'message' => $e->getMessage(),
	    ]);
	    return;
	}

        foreach ($axaServices as $axaItem) {
            $exists = ServiceExternalReference::where('provider_name', 'AXA')
                ->where('external_case_number', $axaItem['numeroExpediente'] ?? null)
                ->where('external_service_id', $axaItem['idServicio'] ?? null)
                ->where('external_provider_service_id', $axaItem['idServicioProveedor'] ?? null)
                ->exists();

            if ($exists) {
                continue;
            }

            $serviceType = ServiceType::firstOrCreate(
                ['name' => $axaItem['tipoServicio'] ?? 'Servicio AXA'],
                ['code' => strtoupper(str_replace(' ', '_', $axaItem['tipoServicio'] ?? 'AXA_SERVICE'))]
            );

            $service = Service::create([
                'folio' => $axaItem['numeroExpediente'] ?? null,
                'source' => 'AXA',
                'service_type_id' => $serviceType->id,
                'insured_name' => $axaItem['nombreBeneficiario'] ?? null,
                'insured_phone' => $axaItem['telefono'] ?? null,
                'origin_address' => $axaItem['direccionOrigen'] ?? null,
                'origin_coordinates' => isset($axaItem['latDireccionOrigen'], $axaItem['lonDireccionOrigen'])
                    ? $axaItem['latDireccionOrigen'] . ',' . $axaItem['lonDireccionOrigen']
                    : null,
                'destination_address' => $axaItem['direccionDestino'] ?? null,
                'destination_coordinates' => isset($axaItem['latDireccionDestino'], $axaItem['lonDireccionDestino'])
                    ? $axaItem['latDireccionDestino'] . ',' . $axaItem['lonDireccionDestino']
                    : null,
                'vehicle' => trim(($axaItem['marca'] ?? '') . ' ' . ($axaItem['modelo'] ?? '') . ' ' . ($axaItem['anio'] ?? '') . ' ' . ($axaItem['placas'] ?? '')),
                'status' => 'new',
                'notes' => trim(($axaItem['problema'] ?? '') . "\n" . ($axaItem['comentarios'] ?? '')),
            ]);

            ServiceExternalReference::create([
                'service_id' => $service->id,
                'provider_name' => 'AXA',
                'external_case_number' => $axaItem['numeroExpediente'] ?? null,
                'external_service_id' => $axaItem['idServicio'] ?? null,
                'external_provider_service_id' => $axaItem['idServicioProveedor'] ?? null,
                'external_status' => 'received',
                'payload' => $axaItem,
            ]);

            $service->events()->create([
                'event_type' => 'integration',
                'title' => 'Servicio recibido desde AXA',
                'description' => 'Servicio importado automáticamente desde la API de AXA.',
                'new_status' => 'new',
            ]);

	    foreach (User::all() as $user) {
	    	Notification::make()
	    	    ->title('Nuevo servicio recibido')
	    	    ->body('Folio: ' . $service->folio . ' / ' . ($service->insured_name ?? 'Sin asegurado'))
	    	    ->success()
	    	    ->sendToDatabase($user);
	    }
        }
    }
}
