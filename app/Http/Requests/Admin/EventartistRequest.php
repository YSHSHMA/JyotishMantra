<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventartistRequest extends FormRequest
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
            'name.*' => 'required|string|min:1',
            'profession'=>'required|array',
            'profession.*' => 'required|string|min:1',
            'image'=>'required|file|mimes:jpg,jpeg,png,webp',
            ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>  translate('the_name_field_is_required'),
            'profession.required' =>  translate('the_profession_field_is_required'),
        ];
    }

}
