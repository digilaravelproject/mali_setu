<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class HomepageHeroResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'functionality_name' => $this->functionality_name,
            'event_name' => $this->event_name,
            'image_url' => $this->image_path
            ? $this->image_path
            : null,
            'web_image_url' => $this->web_image_path
            ? $this->web_image_path
            : null,
            'url' => $this->url,
            'external_url' => $this->external_url,
            'effective_url' => $this->external_url ?: $this->url,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
