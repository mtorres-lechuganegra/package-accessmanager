<?php

namespace LechugaNegra\AccessManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapabilityRoute extends Model
{
    use HasFactory;

    protected $table = 'capability_routes';

    protected $fillable = [
        'name',
        'path',
    ];

    // En el modelo CapabilityRoute
    // public function permissions()
    // {
    //     return $this->hasMany(CapabilityPermission::class, 'capability_route_id');
    // }

    // Relación con los roles que tienen este permiso.
    public function permissions()
    {
        return $this->belongsToMany(CapabilityPermission::class, 'relation_permission_route', 'capability_route_id', 'capability_permission_id');
    }
}
