<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationLog extends Model
{
    protected $fillable = [
        'integration_provider_id',
        'service_id',
        'direction',
        'action',
        'endpoint',
        'status_code',
        'success',
        'request_payload',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'success' => 'boolean',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];
}