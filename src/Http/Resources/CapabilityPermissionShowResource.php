<?php

namespace LechugaNegra\AccessManager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CapabilityPermissionShowResource extends JsonResource
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'routes' => $this->routes()
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'path' => $p->path,
                ]),
            'module' => $this->module()
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'parent_id' => $p->parent_id,
                    'code' => $p->code,
                    'name' => $p->name,
                ]),
        ]);
    }
}

