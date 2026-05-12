<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TourVisitPlaceRequest extends FormRequest
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
            'time' => 'required|array',
            'time.*' => 'required|string|min:1',
            'description' => 'required|array',
            'description.*' => 'required|string|min:1',
        ];

        if (!$this->has('id')) {
            $rules['images'] = 'required';
            $rules['images.*'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_field_is_required!'),
            'name.*.required' => translate('the_name_field_is_required!'),
            'description.required' => translate('the_description_field_is_required!'),
            'description.*.required' => translate('the_description_field_is_required!'),
            
            'time.*.required' => translate('the_time_field_is_required!'),


            'images.required' => translate('the_image_is_required!'),
        ];
    }
}
