<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class EmergencyContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize():bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules():array
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
