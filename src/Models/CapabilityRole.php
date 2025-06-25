<?php

namespace LechugaNegra\AccessManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
