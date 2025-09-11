<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UniversityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $universityId = $this->route('university')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('universities', 'name')->ignore($universityId),
            ],
            'faculty_ids' => 'array|nullable',
            'faculty_ids.*' => 'exists:faculties,id',
            'type' => 'required|in:public,private',
            'province_id' => 'required|exists:provinces,id',
        ];
    }
}
