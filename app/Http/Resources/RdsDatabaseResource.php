<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RdsDatabaseResource extends JsonResource
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
            'db_instance_identifier' => $this->whenHas('db_instance_identifier'),
            'engine' => $this->whenHas('engine'),
            'db_name' => $this->whenHas('db_name'),
            'allocated_storage' => $this->whenHas('allocated_storage', fn($value) => "$value GB"),
            'storage_type' => $this->whenHas('storage_type'),
            'storage_encrypted' => $this->whenHas('storage_encrypted'),
            'publicly_accessible' => $this->whenHas('publicly_accessible'),
            'availability_zone' => $this->whenHas('availability_zone'),
            'multi_az' => $this->whenHas('multi_az'),
            'backup_retention_period' => $this->whenHas('backup_retention_period'),
            'instance_create_time' => $this->whenHas('instance_create_time', fn () => $this->instance_create_time?->toDateTimeString()),
            'status' => $this->whenHas('status', fn($status) => $status->toArray()),
            'engine_version' => $this->whenHas('engine_version'),
            'endpoint_address' => $this->whenHas('endpoint_address'),
            'endpoint_port' => $this->whenHas('endpoint_port'),
            'latest_restorable_time' => $this->whenHas('latest_restorable_time', fn () => $this->latest_restorable_time?->toDateTimeString()),
            'security_group' => new SecurityGroupResource($this->whenLoaded('securityGroup')),
            'servers' => ServerResource::collection($this->whenLoaded('servers')),
            'created_at' => $this->whenHas('created_at', fn () => $this->created_at->diffForHumans()),
        ];
    }
}
