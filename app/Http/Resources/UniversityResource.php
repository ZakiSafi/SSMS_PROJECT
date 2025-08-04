<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class UniversityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'province' => [
                'id' => $this->province->id,
                'name' => $this->province->name,
            ],
            'faculties' => FacultyResource::collection($this->whenLoaded('faculties')),
        ];
    }
}
