<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRegistration extends Model
{
    protected $fillable = [
        'department',
        'shipment',
        'box_number',
        'ul',
        'supplier',
        'AT_number',
        'zone',
        'reason',
        'elapsed_time',
        'validated',
        'invoiced'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
