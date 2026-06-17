<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InsuranceCompany;
use App\Models\ServiceType;

class Service extends Model
{
    protected $fillable = [
        'folio',
        'source',
        'insurance_company_id',
        'service_type_id',
        'provider_id',
        'insured_name',
        'insured_phone',
        'origin_address',
        'origin_coordinates',
        'destination_address',
        'destination_coordinates',
        'vehicle',
        'status',
	'operator_id',
	'unit_id',
	'estimated_arrival_minutes',
        'notes',
	'integration_provider_id',
    ];

    public function insuranceCompany()
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function integrationProvider()
    {
	return $this->belongsTo(IntegrationProvider::class);
    }

    public function events()
    {
        return $this->hasMany(ServiceEvent::class, 'service_id');
    }

    public function externalReferences()
    {
        return $this->hasMany(ServiceExternalReference::class);
    }

    public function operator()
    {
    	return $this->belongsTo(Operator::class);
    }

    public function unit()
    {
    	return $this->belongsTo(Unit::class);
    }

protected static function booted(): void
{
    static::created(function (Service $service) {
        $service->events()->create([
            'event_type' => 'system',
            'title' => 'Servicio creado',
            'description' => 'El servicio fue registrado en el sistema.',
            'new_status' => $service->status,
        ]);
    });

    static::updated(function (Service $service) {
        if ($service->wasChanged('status')) {
            $service->events()->create([
                'event_type' => 'status_change',
                'title' => 'Cambio de estado',
                'description' => 'El estado del servicio fue actualizado.',
                'old_status' => $service->getOriginal('status'),
                'new_status' => $service->status,
            ]);
        }
	if ($service->wasChanged('provider_id')) {
	    $providerName = $service->provider?->name ?? 'Proveedor no especificado';

	    $service->events()->create([
	        'event_type' => 'assignment',
	        'title' => 'Servicio asignado',
	        'description' => 'El servicio fue asignado a: ' . $providerName,
	        'old_status' => $service->getOriginal('status'),
	        'new_status' => $service->status,
	    ]);
	}
	if ($service->wasChanged('status')) {
	    match ($service->status) {
        	'assigned' =>
	            $service->updateQuietly([
        	        'assigned_at' => now(),
	            ]),
        	'accepted' =>
	            $service->updateQuietly([
        	        'accepted_at' => now(),
	            ]),
	        'on_route' =>
        	    $service->updateQuietly([
	                'on_route_at' => now(),
	            ]),
        	'on_scene' =>
	            $service->updateQuietly([
        	        'on_scene_at' => now(),
	            ]),
        	'completed' =>
	            $service->updateQuietly([
        	        'completed_at' => now(),
	            ]),
	        'cancelled' =>
        	    $service->updateQuietly([
	                'cancelled_at' => now(),
	            ]),

	        default => null,
	    };
	}

    });
}


}