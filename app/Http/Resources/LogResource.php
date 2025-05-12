<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user" => [
                "id" => $this->user->id,
                "name" => $this->user->name,
            ],
            "action_type" => $this->action_type,
            "action_description" => $this->action_description,
            "table_name" => $this->table_name,
            "record_id" => $this->record_id,
            "ip_address" => $this->ip_address,
        ];
    }
}
