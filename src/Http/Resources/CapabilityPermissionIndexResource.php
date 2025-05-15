<?php

namespace LechugaNegra\AccessManager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CapabilityPermissionIndexResource extends JsonResource
{
    public function toArray($request): array
    {
        return array_merge(parent::toArray($request), [
            'module' => $this->module()->get()->toArray(),
        ]);
    }
}
