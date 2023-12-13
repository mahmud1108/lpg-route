<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'location_id' => $this->location_id,
            'address' => $this->address,
            'holiday' => $this->holiday,
            'open_hours' => $this->open_hours,
            'inventory' => $this->inventory,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'photo' => $this->photo,
            'admin_id' => $this->admin_id
        ];
    }
}
