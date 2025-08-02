<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
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
            'preferences' => $this->whenHas('preferences'),
            'updated_at' => $this->whenHas('updated_at', fn($date) => $date->diffForHumans())
        ];
    }
}
