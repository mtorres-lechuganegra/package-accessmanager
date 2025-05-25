<?php

namespace LechugaNegra\AccessManager\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class CapabilityPermissionShowResource extends JsonResource
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'route' => $this->route()->get()->toArray(),
            'module' => $this->module()->get()->toArray(),
        ]);
    }
}

