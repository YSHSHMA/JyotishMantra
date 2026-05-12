<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DonateCategoryRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|array',
            'name.*' => 'required|string|min:1',
            ];

            if ($this->filled('id')) {
                $rules['image'] = 'nullable|image|mimes:jpg,png,jpeg,gif,bmp,tif,tiff';
            } else {
                $rules['image'] = 'required|image|mimes:jpg,png,jpeg,gif,bmp,tif,tiff';
            }
    
            return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' =>  translate('the_name_field_is_required'),
            'image.required' => translate('the_image_field_is_required'),
        ];
    }

}
