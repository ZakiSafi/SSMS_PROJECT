<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UniversityRequest extends FormRequest
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
            'name'=> 'required|string|max:255',
            'type' => 'required|in:public,private',
            'province_id' => 'required|exists:provinces,id',
        ];
    }
}
