<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationProvider extends Model
{
    protected $fillable = [
        'insurance_company_id',
        'name',
        'code',
	'driver',
        'base_url',
        'public_key',
        'secret_key',
        'username',
        'password',
        'settings',
        'description',
        'is_active',
	'access_token',
	'token_expires_at',
	'last_login_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'password' => 'encrypted',
        'secret_key' => 'encrypted',
	'access_token' => 'encrypted',
	'token_expires_at' => 'datetime',
	'last_login_at' => 'datetime',
    ];

    public function insuranceCompany()
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

}



