<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


class CitiesAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'short_desc' => 'required',
            'description' => 'required',
            'famous_for' => 'required',
            'longitude'=>'required',
            'latitude'=>'required',
        ];
    }

    public function messages(): array
    {
        return [
            'short_desc.required' => translate('the_short_desciption_field_is_required'),
            'description.required' => translate('the_description_is_required'),
            'famous_for.required' => translate('the_mfamous_for_is_required'),
            'longitude.required' => translate('the_city_is_required'),
            'latitude.required' => translate('the_city_is_required'),
        ];
    }

}