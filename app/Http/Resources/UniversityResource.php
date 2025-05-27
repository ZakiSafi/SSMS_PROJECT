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

        if ($this->id !== Auth::user()->university_id) {
            return []; // or return null, or throw error as you prefer
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'teachers' => $this->teachers,
            'type' => $this->type,
            'province' => [
                'id' => $this->province->id,
                'name' => $this->province->name,
            ],
        ];
    }
}
