<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class AppSectionAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

 
    public function rules()
    {
        return [
            'name' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'name.required' => translate('the_name_field_is_required'),

        ];
    }
}
