<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UniversityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'faculty_ids' => 'array|nullable',
            'faculty_ids.*' => 'exists:faculties,id',
            'type' => 'required|in:public,private',
            'province_id' => 'required|exists:provinces,id',
        ];
    }
}
