<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProvinceRequest extends FormRequest
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
        $provinceId = $this->route('province')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('provinces', 'name')->ignore($provinceId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Province name already exists.',
        ];
    }
}
