<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RdsDatabaseSnapshotResource extends JsonResource
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
            'snapshot_identifier' => $this->whenHas('snapshot_identifier'),
            'snapshot_arn' => $this->whenHas('snapshot_arn'),
            'rds_database' => new RdsDatabaseResource($this->whenLoaded('rdsDatabase')),
            'snapshot_type' => $this->whenHas('snapshot_type'),
            'status' => $this->whenHas('status', fn($status) => $status->toArray()),
            'percent_progress' => $this->whenHas('percent_progress'),
            'encrypted' => $this->whenHas('encrypted'),
            'kms_key_id' => $this->whenHas('kms_key_id'),
            'snapshot_create_time' => $this->whenHas('snapshot_create_time'),
            'created_at' => $this->whenHas('created_at', fn($date) => $date->diffForHumans()),
        ];
    }
}
