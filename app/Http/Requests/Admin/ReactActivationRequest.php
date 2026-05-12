<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReactActivationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'react_license_code'=>'required',
            'react_domain'=>'required'
        ];
    }

    public function messages(): array
    {
        return [
            'react_license_code.required' => translate('license_code_is_required'),
            'react_domain.required' => translate('domain_is_required'),
        ];
    }
}
