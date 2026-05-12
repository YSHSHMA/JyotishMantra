<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EmergencyContactRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'country_code' => 'required',
            'phone' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('name_is_required'),
            'country_code.required' => translate('country_code_is_required'),
            'phone.required' => translate('phone_is_required'),
        ];
    }

}
