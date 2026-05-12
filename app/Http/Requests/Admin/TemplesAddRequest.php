<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


class TemplesAddRequest extends FormRequest
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
            'latitude' => 'required',
            'longitude' => 'required',
            'image' => 'image',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_field_is_required'),
            'latitude.required' => translate('the_name_field_is_required'),
            'longitude.required' => translate('the_name_field_is_required'),
            'image.required' => translate('select_image_is_required'),
        ];
    }

}