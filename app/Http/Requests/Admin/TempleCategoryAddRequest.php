<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


class TempleCategoryAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'short_description' => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_field_is_required'),
            'short_description.required' => translate('the_short_description_field_is_required'),
        ];
    }

}