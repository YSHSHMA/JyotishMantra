<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules():array
    {
        return [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required'
        ];
    }

    /**
     * @return array
     */
    public function messages():array
    {
        return [
            'name.required' => translate('name_is_required').'!',
            'email.required' =>translate('email_is_required').'!',
            'phone.required' =>translate('phone_number_is_required').'!',
        ];
    }
}
