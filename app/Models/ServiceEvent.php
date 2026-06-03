<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceEvent extends Model
{
    protected $fillable = [
        'service_id',
        'event_type',
        'title',
        'description',
        'old_status',
        'new_status',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
