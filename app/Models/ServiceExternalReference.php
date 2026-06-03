<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceExternalReference extends Model
{
    protected $fillable = [
        'service_id',
        'provider_name',
        'external_case_number',
        'external_service_id',
        'external_provider_service_id',
        'external_status',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
