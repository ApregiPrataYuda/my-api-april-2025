<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_submenu ' => $this->id_submenu,
            'id_menu ' => $this->id_menu,
            'title' => $this->title,
            'url' => $this->url,
            'icon' => $this->icon,
            'noted' => $this->noted,
            'is_active' => $this->is_active,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            // 'deleted_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
