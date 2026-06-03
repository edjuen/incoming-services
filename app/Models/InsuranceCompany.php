<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceCompany extends Model
{
    protected $fillable = [
        'name',
        'code',
        'api_enabled',
        'is_active',
    ];

    public function integrationProviders()
    {
        return $this->hasMany(IntegrationProvider::class);
    }
}
