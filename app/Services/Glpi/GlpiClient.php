<?php

namespace App\Services\Glpi;

use App\Models\GlpiIntegration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GlpiClient
{
    public function __construct(
        protected GlpiIntegration $integration
    ) {}

    public static function fromActiveIntegration(): self
    {
        return new self(GlpiIntegration::activeIntegration());
    }

    public function integration(): GlpiIntegration
    {
        return $this->integration;
    }

    public function initSession(): string
    {
        $response = $this->baseHttp()
            ->withHeaders([
                'Authorization' => 'user_token ' . $this->integration->user_token,
            ])
            ->get($this->endpoint('/initSession'));

        if (! $response->successful()) {
            throw new RuntimeException(
                'No se pudo iniciar sesión en GLPI. HTTP '
                . $response->status()
                . ': '
                . $response->body()
            );
        }

        $sessionToken = $response->json('session_token');

        if (! $sessionToken) {
            throw new RuntimeException('GLPI no regresó session_token.');
        }

        return $sessionToken;
    }

    public function testConnection(): array
    {
        $sessionToken = $this->initSession();

        $response = $this->baseHttp()
            ->withHeaders([
                'Session-Token' => $sessionToken,
            ])
            ->get($this->endpoint('/getFullSession'));

        $this->killSession($sessionToken);

        if (! $response->successful()) {
            throw new RuntimeException(
                'La sesión inició, pero falló getFullSession. HTTP '
                . $response->status()
                . ': '
                . $response->body()
            );
        }

        return $response->json();
    }

    public function createTicket(array $input): array
    {
        $sessionToken = $this->initSession();

        try {
            $response = $this->baseHttp()
                ->withHeaders([
                    'Session-Token' => $sessionToken,
                ])
                ->post($this->endpoint('/Ticket'), [
                    'input' => $input,
                ]);

            if (! $response->successful()) {
                throw new RuntimeException(
                    'No se pudo crear el ticket en GLPI. HTTP '
                    . $response->status()
                    . ': '
                    . $response->body()
                );
            }

            $json = $response->json();

            if (! isset($json['id'])) {
                throw new RuntimeException('GLPI creó una respuesta sin ID de ticket: ' . $response->body());
            }

            return $json;
        } finally {
            $this->killSession($sessionToken);
        }
    }

    public function killSession(string $sessionToken): void
    {
        $this->baseHttp()
            ->withHeaders([
                'Session-Token' => $sessionToken,
            ])
            ->get($this->endpoint('/killSession'));
    }

    protected function baseHttp(): PendingRequest
    {
        return Http::timeout(30)
            ->acceptJson()
            ->asJson()
            ->withOptions([
                'verify' => $this->integration->verify_ssl,
            ])
            ->withHeaders([
                'App-Token' => $this->integration->app_token,
            ]);
    }

    protected function endpoint(string $path): string
    {
        return $this->integration->apiBaseUrl() . '/' . ltrim($path, '/');
    }
}
