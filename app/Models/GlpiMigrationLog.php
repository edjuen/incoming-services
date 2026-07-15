<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlpiMigrationLog extends Model
{
    protected $fillable = [
        'service_id',
        'glpi_integration_id',
        'user_id',
        'glpi_ticket_id',
        'action',
        'status',
        'request_payload',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function integration(): BelongsTo
    {
        return $this->belongsTo(GlpiIntegration::class, 'glpi_integration_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
