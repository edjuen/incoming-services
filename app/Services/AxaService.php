<?php

namespace App\Services;

use App\Models\IntegrationProvider;
use Illuminate\Support\Facades\Http;
use App\Models\Service;
use Illuminate\Support\Carbon;
use App\Models\IntegrationLog;

class AxaService
{
    protected IntegrationProvider $integration;

    public function __construct(?IntegrationProvider $integration = null)
    {
    	$this->integration = $integration
    	    ?? IntegrationProvider::where('code', 'AXA_QA')
    	        ->where('is_active', true)
    	        ->firstOrFail();
    }

    public function isMock(): bool
    {
	$settings = $this->integration->settings ?? [];

    	if (is_string($settings)) {
	        $settings = json_decode($settings, true) ?: [];
    	}

	return (bool) ($settings['mock'] ?? false);
    }

    public function settings(): array
    {
    	$settings = $this->integration->settings ?? [];

    	if (is_string($settings)) {
     	   return json_decode($settings, true) ?: [];
     	}

    	return is_array($settings) ? $settings : [];
    }

    public function login(): string
    {
        if ($this->isMock()) {
	    return 'mock-token';
	}

	//$response = Http::post(rtrim($this->integration->base_url, '/') . '/secure/login', [
        //    'userName' => $this->integration->username,
        //    'password' => $this->integration->password,
        //]);

	$response = Http::withoutVerifying()
	    ->post(rtrim($this->integration->base_url, '/') . '/secure/login', [
	        'userName' => $this->integration->username,
	        'password' => $this->integration->password,
    		]);

	$response->throw();

        $token = $response->json('token');

	$this->integration->update([
	    'access_token' => $token,
	    'token_expires_at' => now()->addMinutes(14),
	    'last_login_at' => now(),
	]);

        return $token;
    }

    public function getServices(): array
    {
        if ($this->isMock()) {
            return $this->fakeServices();
        }

	$token = $this->getToken();

        $settings = $this->settings();

        //$response = Http::withToken($token)
        //    ->get(rtrim($this->integration->base_url, '/') . '/lecturaServicios', [
        //        'rfc' => $settings['rfc'] ?? null,
        //    ]);

	$response = Http::withoutVerifying()
            ->withToken($token)
            ->get(rtrim($this->integration->base_url, '/') . '/lecturaServicios', [
                'rfc' => $settings['rfc'] ?? null,
            ]);

        $response->throw();

        return $response->json() ?? [];
    }

    public function getToken(): string
    {
    	if (
    	    $this->integration->access_token &&
    	    $this->integration->token_expires_at &&
    	    $this->integration->token_expires_at->isFuture()
    	) {
    	    return $this->integration->access_token;
    	}

    	return $this->login();
    }

    public function getIntegration(): IntegrationProvider
    {
        return $this->integration;
    }

    public function fakeServices(): array
	{
	    return [
	        [
	            "numeroExpediente" => "EXP-TEST-004",
        	    "idServicio" => "2124",
	            "idServicioProveedor" => "18",
        	    "claveProveedor" => "4004",
	            "marca" => "Nissan",
        	    "modelo" => "Versa",
	            "anio" => "2020",
	            "placas" => "ABC123",
	            "direccionOrigen" => "Origen de prueba",
        	    "latDireccionOrigen" => "28.6353",
	            "lonDireccionOrigen" => "-106.0889",
        	    "direccionDestino" => "Destino de prueba",
	            "latDireccionDestino" => "28.6400",
        	    "lonDireccionDestino" => "-106.0900",
	            "nombreBeneficiario" => "Juan Pérez",
        	    "telefono" => "6141234567",
	            "tipoServicio" => "Remolque",
        	    "problema" => "Avería",
	            "comentarios" => "Servicio simulado AXA",
        	    "minutosMaximoArribo" => "30",
	            "costoServicio" => "0",
	        ],
	    ];
    }

    public function acceptService(Service $service): array
    {
        $reference = $service->externalReferences()
            ->where('provider_name', 'AXA')
            ->firstOrFail();

        $operator = $service->operator;
        $unit = $service->unit;

        if (! $operator || ! $unit) {
            throw new \Exception('El servicio necesita operador y unidad antes de aceptarse en AXA.');
        }

        $payload = [
            'expediente' => $reference->external_case_number,
            'idServicio' => (int) $reference->external_service_id,
            'idServicioProveedor' => (int) $reference->external_provider_service_id,
            'minutosArribo' => (int) ($service->estimated_arrival_minutes ?? 30),
            'unidadProveedor' => [
                'marca' => $unit->brand,
                'modelo' => $unit->year,
                'color' => $unit->color,
                'placas' => $unit->plates,
                'chasis' => $unit->vin,
            ],
            'operador' => [
                'nombre' => $operator->first_name,
                'apellidoPaterno' => $operator->last_name,
                'apellidoMaterno' => $operator->second_last_name,
            ],
            'claveProveedor' => $reference->payload['claveProveedor'] ?? null,
        ];

        if ($this->isMock()) {
            $reference->update([
                'external_status' => 'accepted_mock',
            ]);

            $service->events()->create([
                'event_type' => 'integration',
                'title' => 'Aceptación AXA simulada',
                'description' => 'Modo mock activo. No se envió petición real a AXA.',
                'old_status' => $service->getOriginal('status'),
                'new_status' => $service->status,
            ]);

	    $this->logIntegration([
		    'service_id' => $service->id,
		    'direction' => 'outgoing',
		    'action' => 'accept',
		    'endpoint' => '/status/Aceptacion',
		    'status_code' => 200,
		    'success' => true,
		    'request_payload' => $payload,
		    'response_payload' => [
		        'mock' => true,
		        'message' => 'Aceptación simulada correctamente.',
		    ],
		]);

            return [
                'mock' => true,
                'message' => 'Aceptación simulada correctamente.',
                'payload' => $payload,
            ];
        }

        $token = $this->getToken();

        $response = Http::withoutVerifying()
            ->withToken($token)
            ->post(rtrim($this->integration->base_url, '/') . '/status/Aceptacion', $payload);

        $response->throw();

	$this->logIntegration([
	    'service_id' => $service->id,
	    'direction' => 'outgoing',
	    'action' => 'accept',
	    'endpoint' => '/status/Aceptacion',
	    'status_code' => $response->status(),
	    'success' => true,
	    'request_payload' => $payload,
	    'response_payload' => ['body' => $response->json() ?? $response->body(),],
	]);

        $reference->update([
            'external_status' => 'accepted',
        ]);

        $service->events()->create([
            'event_type' => 'integration',
            'title' => 'Aceptación enviada a AXA',
            'description' => 'El servicio fue aceptado correctamente ante AXA.',
            'old_status' => $service->getOriginal('status'),
            'new_status' => $service->status,
        ]);




        return [
	    'status' => $response->status(),
	    'body' => $response->json() ?? $response->body(),
	];
    }

public function contactService(Service $service): array
{
    $reference = $service->externalReferences()
        ->where('provider_name', 'AXA')
        ->firstOrFail();

    $operator = $service->operator;
    $unit = $service->unit;

    if (! $operator || ! $unit) {
        throw new \Exception('El servicio necesita operador y unidad antes de reportar contacto a AXA.');
    }

    $payload = [
        'expediente' => $reference->external_case_number,
        'idServicio' => (int) $reference->external_service_id,
        'idServicioProveedor' => (int) $reference->external_provider_service_id,
        'horaContacto' => now()->format('H:i'),
        'codigo' => 'GR',

        'unidadProveedor' => [
            'marca' => $unit->brand,
            'modelo' => $unit->year,
            'color' => $unit->color,
            'placas' => $unit->plates,
            'chasis' => $unit->vin,
        ],

        'nombreOperador' => [
            'nombre' => $operator->first_name,
            'apellidoPaterno' => $operator->last_name,
            'apellidoMaterno' => $operator->second_last_name,
        ],
        'claveOperador' => $reference->payload['claveProveedor'] ?? null,
    ];

    if ($this->isMock()) {
        $reference->update([
            'external_status' => 'contact_mock',
        ]);

        $service->events()->create([
            'event_type' => 'integration',
            'title' => 'Contacto AXA simulado',
            'description' => 'Modo mock activo. No se envió petición real a AXA.',
            'old_status' => $service->getOriginal('status'),
            'new_status' => $service->status,
        ]);

	    $this->logIntegration([
		    'service_id' => $service->id,
		    'direction' => 'outgoing',
		    'action' => 'accept',
		    'endpoint' => '/status/Aceptacion',
		    'status_code' => 200,
		    'success' => true,
		    'request_payload' => $payload,
		    'response_payload' => [
		        'mock' => true,
		        'message' => 'Aceptación simulada correctamente.',
		    ],
		]);

        return [
            'mock' => true,
            'message' => 'Contacto simulado correctamente.',
            'payload' => $payload,
        ];
    }

    $token = $this->getToken();

    $response = Http::withoutVerifying()
        ->withToken($token)
        ->post(rtrim($this->integration->base_url, '/') . '/status/Contacto', $payload);

    $response->throw();

	$this->logIntegration([
	    'service_id' => $service->id,
	    'direction' => 'outgoing',
	    'action' => 'contact',
	    'endpoint' => '/status/Contacto',
	    'status_code' => $response->status(),
	    'success' => true,
	    'request_payload' => $payload,
	    'response_payload' => ['body' => $response->json() ?? $response->body(),],
	]);

    $reference->update([
        'external_status' => 'contact',
    ]);

    $service->events()->create([
        'event_type' => 'integration',
        'title' => 'Contacto enviado a AXA',
        'description' => 'Se reportó contacto/en sitio correctamente ante AXA.',
        'old_status' => $service->getOriginal('status'),
        'new_status' => $service->status,
    ]);

    return $response->json() ?? [];
}

public function finishService(Service $service): array
{
    $reference = $service->externalReferences()
        ->where('provider_name', 'AXA')
        ->firstOrFail();

    $operator = $service->operator;
    $unit = $service->unit;

    if (! $operator || ! $unit) {
        throw new \Exception('El servicio necesita operador y unidad antes de finalizar en AXA.');
    }

    $payload = [
        'expediente' => $reference->external_case_number,
        'idServicio' => (int) $reference->external_service_id,
        'idServicioProveedor' => (int) $reference->external_provider_service_id,
        'horaFin' => now()->format('H:i'),
        'unidadProveedor' => [
            'marca' => $unit->brand,
            'modelo' => $unit->year,
            'color' => $unit->color,
            'placas' => $unit->plates,
            'chasis' => $unit->vin,
        ],
        'nombreOperador' => [
            'nombre' => $operator->first_name,
            'apellidoPaterno' => $operator->last_name,
            'apellidoMaterno' => $operator->second_last_name,
        ],
        'claveOperador' => $reference->payload['claveProveedor'] ?? null,
    ];

    if ($this->isMock()) {
        $reference->update(['external_status' => 'finished_mock']);

        $service->events()->create([
            'event_type' => 'integration',
            'title' => 'Finalización AXA simulada',
            'description' => 'Modo mock activo. No se envió petición real a AXA.',
            'old_status' => $service->getOriginal('status'),
            'new_status' => $service->status,
        ]);

	    $this->logIntegration([
		    'service_id' => $service->id,
		    'direction' => 'outgoing',
		    'action' => 'finish',
		    'endpoint' => '/status/Finalizacion',
		    'status_code' => 200,
		    'success' => true,
		    'request_payload' => $payload,
		    'response_payload' => [
		        'mock' => true,
		        'message' => 'Aceptación simulada correctamente.',
		    ],
		]);

        return [
            'mock' => true,
            'message' => 'Finalización simulada correctamente.',
            'payload' => $payload,
        ];
    }

    $token = $this->getToken();

    $response = Http::withoutVerifying()
        ->withToken($token)
        ->post(rtrim($this->integration->base_url, '/') . '/status/Finalizacion', $payload);

    $response->throw();

	$this->logIntegration([
	    'service_id' => $service->id,
	    'direction' => 'outgoing',
	    'action' => 'accept',
	    'endpoint' => '/status/Finalizacion',
	    'status_code' => $response->status(),
	    'success' => true,
	    'request_payload' => $payload,
	    'response_payload' => ['body' => $response->json() ?? $response->body(),],
	]);

    $reference->update(['external_status' => 'finished']);

    $service->events()->create([
        'event_type' => 'integration',
        'title' => 'Finalización enviada a AXA',
        'description' => 'Se reportó finalización correctamente ante AXA.',
        'old_status' => $service->getOriginal('status'),
        'new_status' => $service->status,
    ]);

    return $response->json() ?? [];
}

public function cancelService(Service $service, string $rejectCode): array
{
    $reference = $service->externalReferences()
        ->where('provider_name', 'AXA')
        ->firstOrFail();

    $operator = $service->operator;

    $operatorName = $operator
        ? trim($operator->first_name . ' ' . $operator->last_name)
        : 'Operador no asignado';

    $payload = [
        'expediente' => $reference->external_case_number,
        'idServicio' => (int) $reference->external_service_id,
        'idServicioProveedor' => (int) $reference->external_provider_service_id,
        'operador' => $operatorName,
        'claveProveedor' => $reference->payload['claveProveedor'] ?? null,
        'codigoRechazo' => $rejectCode,
    ];

    if ($this->isMock()) {
        $reference->update(['external_status' => 'cancelled_mock']);

        $service->events()->create([
            'event_type' => 'integration',
            'title' => 'Cancelación AXA simulada',
            'description' => 'Modo mock activo. No se envió petición real a AXA. Código: ' . $rejectCode,
            'old_status' => $service->getOriginal('status'),
            'new_status' => $service->status,
        ]);

	    $this->logIntegration([
		    'service_id' => $service->id,
		    'direction' => 'outgoing',
		    'action' => 'cancel',
		    'endpoint' => '/status/RechazoCancelacion',
		    'status_code' => 200,
		    'success' => true,
		    'request_payload' => $payload,
		    'response_payload' => [
		        'mock' => true,
		        'message' => 'Aceptación simulada correctamente.',
		    ],
		]);

        return [
            'mock' => true,
            'message' => 'Cancelación simulada correctamente.',
            'payload' => $payload,
        ];
    }

    $token = $this->getToken();

    $response = Http::withoutVerifying()
        ->withToken($token)
        ->post(rtrim($this->integration->base_url, '/') . '/status/RechazoCancelacion', $payload);

    $response->throw();

	$this->logIntegration([
	    'service_id' => $service->id,
	    'direction' => 'outgoing',
	    'action' => 'accept',
	    'endpoint' => '/status/RechazoCancelacion',
	    'status_code' => $response->status(),
	    'success' => true,
	    'request_payload' => $payload,
	    'response_payload' => ['body' => $response->json() ?? $response->body(),],
	]);

    $reference->update(['external_status' => 'cancelled']);

    $service->events()->create([
        'event_type' => 'integration',
        'title' => 'Cancelación enviada a AXA',
        'description' => 'Se reportó cancelación/rechazo correctamente ante AXA. Código: ' . $rejectCode,
        'old_status' => $service->getOriginal('status'),
        'new_status' => $service->status,
    ]);

    return $response->json() ?? [];
}

protected function logIntegration(array $data): void
{
    IntegrationLog::create([
        'integration_provider_id' => $this->integration->id,
        'service_id' => $data['service_id'] ?? null,
        'direction' => $data['direction'] ?? null,
        'action' => $data['action'] ?? null,
        'endpoint' => $data['endpoint'] ?? null,
        'status_code' => $data['status_code'] ?? null,
        'success' => $data['success'] ?? false,
        'request_payload' => $data['request_payload'] ?? null,
        'response_payload' => $data['response_payload'] ?? null,
        'error_message' => $data['error_message'] ?? null,
    ]);
}

}