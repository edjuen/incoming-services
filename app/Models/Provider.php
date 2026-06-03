<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'city',
        'is_active',
    ];

    public function operators()
    {
    	return $this->hasMany(Operator::class);
    }

    public function units()
    {
    	return $this->hasMany(Unit::class);
    }
}