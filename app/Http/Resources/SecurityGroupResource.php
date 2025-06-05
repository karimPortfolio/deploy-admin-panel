<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SecurityGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenHas('id'),
            'group_id' => $this->whenHas('group_id'),
            'name' => $this->whenHas('name'),
            'description' => $this->whenHas('description'),
            'vpc_id' => $this->whenHas('vpc_id'),
            'servers_count' => $this->whenCounted('servers'),
            'servers' => ServerResource::collection($this->whenLoaded('servers')),
            'created_at' => $this->whenHas('created_at', function () {
                return $this->created_at->diffForHumans();
            }),
        ];
    }
}
