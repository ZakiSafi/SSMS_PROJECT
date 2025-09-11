<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Faculty;

class FacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $facultyId = $this->route('faculty')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('faculties', 'name')->ignore($facultyId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Faculty name already exists.',
        ];
    }
}
