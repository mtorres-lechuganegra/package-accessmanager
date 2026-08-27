<?php

namespace LechugaNegra\AccessManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use LechugaNegra\AccessManager\Services\CapabilityLogService;

class CapabilityRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'capability_roles';

    protected $fillable = [
        'name',
        'code',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string', // Esto lo convierte en string
    ];

    // Relación con los permisos de este rol.
    public function permissions()
    {
        return $this->belongsToMany(CapabilityPermission::class, 'relation_role_permission', 'capability_role_id', 'capability_permission_id');
    }

    // Relación con el usuario que creó este rol.
    public function createdBy()
    {
        return $this->belongsTo(config('accessmanager.user_entity.model'), 'created_by');
    }

    protected static function booted()
    {
        $events = ['created', 'updated', 'deleted'];

        foreach ($events as $event) {
            static::$event(function ($model) use ($event) {

                $safeFields = ['id', 'name', 'code', 'status', 'created_by'];
                $logData = collect($model->toArray())->only($safeFields)->toArray();

                CapabilityLogService::register([
                    'data_id' => $model->id,
                    'data_code' => $model->code ?? null,
                    'data_name' => $model->name ?? null,
                    'data_type' => 'role',
                    'data_date' => $model->created_at ?? null,
                    'data_status' => $model->status ?? null,
                    'action' => $event,
                    'user_id' => (function () {
                        try {
                            return Auth::guard('api')->id();
                        } catch (\Throwable $e) {
                            return null;
                        }
                    })(),
                    'log_data' => $logData,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });
        }
    }
}
