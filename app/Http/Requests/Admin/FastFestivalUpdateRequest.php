<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FastFestivalUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            
            'en_description' => 'required',
            'hi_description' => 'required',
            // 'image' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
       
            'en_description.*.required' => translate('The en description field is required.'),
            'hi_description.*.required' => translate('The hi description field is required.'),
            'image.image' => translate('The file must be an image.'),
            'image.mimes' => translate('Only JPG, PNG, JPEG, GIF files are allowed.'),
            'image.max' => translate('Maximum file size allowed is 2MB.')
        ];
    }
}
