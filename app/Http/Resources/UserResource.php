<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $role = $this->roles->first();
        return [
            "id" => $this->id,
            "name" => $this->name,
            "university" => [
                'id' => $this->university->id,
                'name' => $this->university->name,
            ],
            "email" => $this->email,
            "password" => $this->password,
            "role" => $role ? [
                'id' => $role->id,
                'name' => $role->name,
            ] : null,
            'permissions' => $this->getAllPermissions()->pluck('name')


        ];
    }
}
