<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'company_name' => $this->whenHas('company_name'),
            'email' => $this->whenHas('email'),
            'active' => $this->whenHas('grant_access'),
            'photo' => $this->whenLoaded("media", function ($media) {
                return count($media) > 0 ? $media[0]->getFullUrl() : "/src/img/avatar.png";
            }),
            'preferences' => UserPreferenceResource::collection($this->whenLoaded('preferences'))
        ];
    }
}
