<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

class GlpiIntegration extends Model
{
    protected $fillable = [
        'name',
        'base_url',
        'app_token',
        'user_token',
        'default_entity_id',
        'verify_ssl',
        'active',
    ];

    protected $casts = [
        'app_token' => 'encrypted',
        'user_token' => 'encrypted',
        'default_entity_id' => 'integer',
        'verify_ssl' => 'boolean',
        'active' => 'boolean',
    ];

    public function migrationLogs(): HasMany
    {
        return $this->hasMany(GlpiMigrationLog::class);
    }

    public static function activeIntegration(): self
    {
        $integration = self::query()
            ->where('active', true)
            ->latest('id')
            ->first();

        if (! $integration) {
            throw new RuntimeException('No existe una integración GLPI activa.');
        }

        if (! $integration->base_url || ! $integration->app_token || ! $integration->user_token) {
            throw new RuntimeException('La integración GLPI activa está incompleta.');
        }

        return $integration;
    }

    public function apiBaseUrl(): string
    {
        $baseUrl = rtrim($this->base_url, '/');

        if (str_ends_with($baseUrl, '/apirest.php')) {
            return $baseUrl;
        }

        return $baseUrl . '/apirest.php';
    }
}