<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


class TemplesPackageAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_field_is_required'),
        ];
    }

}