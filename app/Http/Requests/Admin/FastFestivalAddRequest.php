<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FastFestivalAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_name' => 'required',
            
            'event_type' => 'required',
          
            'en_description' => 'required',
        
            'hi_description' => 'required',
           
            'image' => 'required|image|mimes:jpg,jpeg,png',
             
        ];
    }

    public function messages(): array
    {
        return [
            'event_name.required' => translate('the_event_name_field_is_required'),
            'event_type.required' => translate('the_event_type_field_is_required'),
            'en_description.required' => translate('the_en_description_field_is_required'),
            'hi_description.required' => translate('the_hi_description_field_is_required'),
            'image.required' => translate('the_image_is_required'),
        ];
    }
}
