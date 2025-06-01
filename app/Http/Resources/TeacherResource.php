<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return
            [
                'id' => $this->id,
                'university' => [
                    'id' => $this->university->id,
                    'name' => $this->university->name,

                ],
                'academic_year' => $this->academic_year,
                'total_teachers' => $this->total_teachers,


            ];
    }
}
