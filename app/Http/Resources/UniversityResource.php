<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UniversityResource extends JsonResource
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
            'name' => $this->name,
            'teachers' => $this->teachers,
            'type' => $this->type,
            'province' => [
                'id' => $this->province->id,
                'name' => $this->province->name,
            ]
        ];
    }
}
