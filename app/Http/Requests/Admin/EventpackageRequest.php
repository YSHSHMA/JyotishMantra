<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventpackageRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'package_name' => 'required|array',
            'package_name.*' => 'required|string|min:1',
            'description'=>'required|array',
            'description.*' => 'required|string|min:1',
            ];
    }

    public function messages(): array
    {
        return [
            'package_name.required' =>  translate('the_name_field_is_required'),
            'description.required' =>  translate('the_description_field_is_required'),
        ];
    }

}
