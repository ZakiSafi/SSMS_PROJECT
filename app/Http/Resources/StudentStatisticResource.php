<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "academic_year" => $this->academic_year,
            "department" => [
                "department_id" => $this->department->id,
                "name" => $this->department->name,
            ],
            "classroom" => $this->classroom,
            'semester_number' => $this->semester_number,
            'shift' => $this->shift,
            'season' => $this->season,
            "male_total" => $this->male_total,
            "female_total" => $this->female_total,
            "student_type" => $this->student_type,
        ];
    }
}
