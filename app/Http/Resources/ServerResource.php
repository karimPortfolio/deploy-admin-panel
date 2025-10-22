<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServerResource extends JsonResource
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
            'name' => $this->whenHas('name'),
            'instance_id' => $this->instance_id,
            'image_id' => $this->whenHas('image_id'),
            'instance_type' => $this->whenHas('instance_type', fn($value) => $value?->toArray()),
            'os_family' => $this->whenHas('os_family', fn($value) => $value?->toArray()),
            'status' => $this->whenHas('status', fn($value) => $value?->toArray()),
            'public_ip_address' => $this->whenHas('public_ip_address'),
            'vpc_id' => $this->whenHas('vpc_id'),
            'subnet_id' => $this->whenHas('subnet_id'),
            'ssh_key' => new SshKeyResource($this->whenLoaded('sshKey')),
            'security_group' => new SecurityGroupResource($this->whenLoaded('securityGroup')),
            'rds_databases' => RdsDatabaseResource::collection($this->whenLoaded('rdsDatabases')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'created_at' => $this->whenHas('created_at', fn($value) => $value?->diffForHumans()),
        ];
    }
}
