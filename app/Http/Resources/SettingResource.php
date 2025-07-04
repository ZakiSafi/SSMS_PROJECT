<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "university" => [
                "university_id" => $this->university->id,
                "name" => $this->university->name,
            ],
            "email" => $this->email,
            "phone" => $this->phone,
            "address" => $this->address,
            "image" => $this->image,

        ];
    }
}
