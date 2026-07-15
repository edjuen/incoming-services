<?php

namespace App\Services\Glpi;

use App\Models\GlpiIntegration;
use App\Models\Service;
use App\Models\User;

class GlpiPayloadBuilder
{
    public function buildTicketInput(Service $service, User $user, GlpiIntegration $integration): array
    {
        $content = $this->buildHtmlContent($service, $user);

        $input = [
            'name' => $this->buildTicketTitle($service),
            'content' => $content,

            // Siempre entra a GLPI en proceso.
            // El cierre se hará manualmente en GLPI después de la verificación.
            'status' => 2,

            'entities_id' => $integration->default_entity_id,

            // Usuario que dio clic en Laravel, mapeado con su ID real de GLPI.
            'users_id_recipient' => $user->glpi_user_id,
            '_users_id_requester' => $user->glpi_user_id,

            'urgency' => 3,
            'impact' => 3,
            'priority' => 3,
        ];

        return array_filter($input, fn ($value) => ! is_null($value));
    }

    protected function buildTicketTitle(Service $service): string
    {
        $folio = data_get($service, 'folio')
            ?: data_get($service, 'axa_numero_expediente')
            ?: data_get($service, 'id');

        return 'AXA - Folio ' . $folio;
    }

    protected function buildHtmlContent(Service $service, User $user): string
    {
        $rows = [
            'Origen del registro' => 'Sistema Laravel AXA',
            'Migrado por' => $user->name . ' / Laravel ID: ' . $user->id . ' / GLPI ID: ' . $user->glpi_user_id,
            'Fecha de migración' => now()->format('Y-m-d H:i:s'),

            'ID servicio Laravel' => data_get($service, 'id'),
            'Folio Laravel' => data_get($service, 'folio'),
            'Estado Laravel' => data_get($service, 'status'),

            'Número expediente AXA' => data_get($service, 'axa_numero_expediente'),
            'ID servicio AXA' => data_get($service, 'axa_id_servicio'),
            'ID servicio proveedor AXA' => data_get($service, 'axa_id_servicio_proveedor'),
            'Clave proveedor AXA' => data_get($service, 'axa_clave_proveedor'),

            'Aseguradora' => data_get($service, 'insuranceCompany.name'),
            'Integración' => data_get($service, 'integrationProvider.name'),
            'Tipo de servicio' => data_get($service, 'serviceType.name'),

            'Asegurado' => data_get($service, 'insured_name'),
            'Teléfono asegurado' => data_get($service, 'insured_phone'),

            'Origen dirección' => data_get($service, 'origin_address'),
            'Origen coordenadas' => data_get($service, 'origin_coordinates'),

            'Destino dirección' => data_get($service, 'destination_address'),
            'Destino coordenadas' => data_get($service, 'destination_coordinates'),

            'Problema' => data_get($service, 'problem'),
            'Notas / Comentarios' => data_get($service, 'notes'),

            'Proveedor asignado' => data_get($service, 'provider.name'),
            'Operador asignado' => data_get($service, 'operator_full_name') ?: data_get($service, 'operator.name'),
            'Unidad asignada' => data_get($service, 'unit_label') ?: data_get($service, 'unit.name'),

            'Vehículo' => data_get($service, 'vehicle'),
            'Marca vehículo' => data_get($service, 'vehicle_brand'),
            'Modelo vehículo' => data_get($service, 'vehicle_model'),
            'Año vehículo' => data_get($service, 'vehicle_year'),
            'Placas vehículo' => data_get($service, 'vehicle_plates'),
            'VIN vehículo' => data_get($service, 'vehicle_vin'),

            'Costo servicio' => data_get($service, 'service_cost'),
            'ETA minutos' => data_get($service, 'estimated_arrival_minutes'),
            'Creado en Laravel' => optional(data_get($service, 'created_at'))->format('Y-m-d H:i:s'),
        ];

        $html = '<h3>Servicio AXA migrado desde Laravel</h3>';
        $html .= '<table border="1" cellpadding="6" cellspacing="0">';

        foreach ($rows as $label => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $html .= '<tr>';
            $html .= '<td><strong>' . e($label) . '</strong></td>';
            $html .= '<td>' . nl2br(e((string) $value)) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    protected function coordinates(mixed $latitude, mixed $longitude): ?string
    {
        if (! $latitude || ! $longitude) {
            return null;
        }

        return $latitude . ', ' . $longitude;
    }
}
