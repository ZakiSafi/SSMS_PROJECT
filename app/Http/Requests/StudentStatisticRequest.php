<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentStatisticRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'academic_year' => 'required|string',
            'university_id' => 'required|exists:universities,id',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'classroom' => 'required|string',
            'male_total' => 'required|integer|min:0',
            'female_total' => 'required|integer|min:0',
            'student_type' => 'required|in:new,current,graduated',
            "shift"=>"required|string|in:day,night",
            "season"=>"required|string|in:spring,autumn",
        ];
    }
}
