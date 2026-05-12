<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventCategoryRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_name' => 'required|array',
            'category_name.*' => 'required|string|min:1',
            ];
    }

    public function messages(): array
    {
        return [
            'category_name.required' =>  translate('the_name_field_is_required'),
        ];
    }

}
