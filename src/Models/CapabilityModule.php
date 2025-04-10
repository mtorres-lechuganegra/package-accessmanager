<?php

namespace Lechuganegra\AccessManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapabilityModule extends Model
{
    use HasFactory;

    protected $table = 'capability_modules';

    protected $fillable = [
        'parent_id',
        'code',
        'name',
    ];

    // Relación con el módulo padre (self-referencing)
    public function parent()
    {
        return $this->belongsTo(CapabilityModule::class, 'parent_id');
    }

    // Relación con los módulos hijos
    public function children()
    {
        return $this->hasMany(CapabilityModule::class, 'parent_id');
    }

    // En el modelo CapabilityModule
    public function permissions()
    {
        return $this->hasMany(CapabilityPermission::class, 'capability_module_id');
    }
}
