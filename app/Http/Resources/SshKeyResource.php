<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SshKeyResource extends JsonResource
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
            'public_key' => $this->whenHas('public_key', $this->getPublicKey()),
            'servers_count' => $this->whenCounted('servers_count'),
            'servers' => ServerResource::collection($this->whenLoaded('servers')),
            'created_at' => $this->whenHas('created_at', function () {
                return $this->created_at->diffForHumans();
            }),       
        ];
    }

    private function getPublicKey()
    {
        // return only the first 10 characters of the public key to the client
        return substr($this->public_key, 0, 40);
    }
}
