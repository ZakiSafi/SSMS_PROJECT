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
            "academic_year" =>[
                'academic_year_id' => $this->academic_year->id,
                'year' => $this->academic_year->year,
                'label' => $this->academic_year->label,
            ],
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
            "classroom" => [
                "classroom_id" => $this->classroom->id,
                "name" => $this->classroom->name,
            ],
            'semester_number' => $this->semester_number,
            'shift' => $this->shift,
            'season' => $this->season,
            "male_total" => $this->male_total,
            "female_total" => $this->female_total,
            "student_type" => $this->student_type,
        ];
    }
}
