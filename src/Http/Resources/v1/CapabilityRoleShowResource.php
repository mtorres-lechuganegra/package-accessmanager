<?php

namespace LechugaNegra\AccessManager\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class CapabilityRoleShowResource extends JsonResource
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'permissions' => $this->permissions()->orderBy('name', 'asc')->get()->toArray(),
        ]);
    }
}

