<?php

namespace LechugaNegra\AccessManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LechugaNegra\AccessManager\Scopes\VisiblePermissionScope;

class CapabilityPermission extends Model
{
    use HasFactory;

    protected $table = 'capability_permissions';

    protected $fillable = [
        'group',
        'code',
        'name',
        'type',
        'hidden',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new VisiblePermissionScope);
    }

    public function roles()
    {
        return $this->belongsToMany(CapabilityRole::class, 'relation_role_permission', 'capability_permission_id', 'capability_role_id');
    }

    public function routes()
    {
        return $this->belongsToMany(CapabilityRoute::class, 'relation_permission_route', 'capability_permission_id', 'capability_route_id');
    }
}
