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
            'academic_year_id' => 'required|exists:universities,id',
            'university_id' => 'required|exists:universities,id',
            'faculty_id' => 'required|exists:universities,id',
            'department_id' => 'required|exists:universities,id',
            'classroom_id' => 'required|exists:universities,id',
            'gender' => ' required|in:male,female',
            'student_type' => 'required|in:new,current,graduated',
        ];
    }
}
