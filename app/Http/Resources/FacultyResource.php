<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacultyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // Include universities if needed
           "universities" => $this->universities->map(fn($uni) => [
    "id" => $uni->id,
    "name" => $uni->name,
]),

        ];
    }
}
