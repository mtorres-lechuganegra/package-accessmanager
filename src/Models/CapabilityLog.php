<?php

namespace LechugaNegra\AccessManager\Models;

use Illuminate\Database\Eloquent\Model;

class CapabilityLog extends Model
{
    public $timestamps = false;

    protected $table = 'capability_logs';

    protected $fillable = [
        'data_id',
        'data_code',
        'data_name',
        'data_type',
        'data_date',
        'data_status',
        'action',
        'user_id',
        'log_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'log_data' => 'array',
        'data_date' => 'datetime',
    ];
}
