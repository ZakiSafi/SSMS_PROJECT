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
            "academic_year_id" => $this->academic_year_id,
            "university_id" => $this->university_id,
            "faculty_id" => $this->name,
            "department_id" => $this->department_id,
            "classroom_id" => $this->total_students,
            "gender" => $this->gender,
            "student_type" => $this->student_type,
        ];
    }
}
