<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivilegeUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'menu_id' => $this->menu_id,
            'c' => $this->c,
            'r' => $this->r,
            'u' => $this->u,
            'd' => $this->d,
            'user' => new UserResource($this->whenLoaded('user')),
            'menu' => new MenuResource($this->whenLoaded('menu')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
