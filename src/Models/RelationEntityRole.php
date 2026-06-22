<?php

namespace LechugaNegra\AccessManager\Models;

use Illuminate\Database\Eloquent\Model;

class RelationEntityRole extends Model
{
    protected $table = 'relation_entity_role';

    protected $fillable = [
        'entity_module',
        'entity_id',
        'capability_role_id',
    ];

    public function role()
    {
        return $this->belongsTo(CapabilityRole::class, 'capability_role_id');
    }
}
