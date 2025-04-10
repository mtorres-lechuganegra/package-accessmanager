<?php

namespace Lechuganegra\AccessManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapabilityPermission extends Model
{
    use HasFactory;

    protected $table = 'capability_permissions';

    protected $fillable = [
        'code',
        'name',
        'type',
        'capability_module_id',
        'capability_route_id',
    ];

    // Relación con el modelo CapabilityModule.
    public function module()
    {
        return $this->belongsTo(CapabilityModule::class, 'capability_module_id');
    }

    // Relación con el modelo CapabilityRoute.
    public function route()
    {
        return $this->belongsTo(CapabilityRoute::class, 'capability_route_id');
    }

    // Relación con los roles que tienen este permiso.
    public function roles()
    {
        return $this->belongsToMany(CapabilityRole::class, 'relation_role_permission', 'capability_permission_id', 'capability_role_id');
    }
}
