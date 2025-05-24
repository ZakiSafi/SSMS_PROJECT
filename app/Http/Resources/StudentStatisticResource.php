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
            "id"=> $this->id,
            "academic_year" => $this->academic_year,
            "university" => [
                "university_id" => $this->university->id,
                "name" => $this->university->name,
            ],
            "faculty" => [
                "faculty_id" => $this->faculty->id,
                "name" => $this->faculty->name,
            ],
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
