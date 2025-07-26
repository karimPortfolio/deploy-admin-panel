<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'data' => $this->whenHas('data'),
            'read_at' => $this->whenHas('read_at'),
            'created_at' => $this->whenHas('created_at', fn ($date) => $date->diffForHumans())
        ];
    }
}
