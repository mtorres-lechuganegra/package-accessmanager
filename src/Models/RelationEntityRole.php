<?php

namespace Lechuganegra\AccessManager\Models;

use Illuminate\Database\Eloquent\Model;

class RelationEntityRole extends Model
{
    protected $table = 'relation_entity_role';

    public function role()
    {
        return $this->belongsTo(CapabilityRole::class, 'capability_role_id');
    }
}
