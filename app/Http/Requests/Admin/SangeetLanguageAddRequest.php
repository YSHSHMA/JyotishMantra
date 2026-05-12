<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class SangeetLanguageAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

 
    public function rules()
    {
        return [
            'name' => 'required|array',
            'name.*' => 'required|string',
            'name.0' => 'required|unique:sangeet_languages,name'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => translate('the_name_field_is_required'),
            'name.0.required' => translate('the_name_field_is_required'),
            'name.0.unique' => translate('The_language_name_has_already_been_taken'),
        ];
    }
}
