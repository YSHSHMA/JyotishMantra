<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class ChadhavaAddRequest extends FormRequest
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
            'image' => 'required|image',
            'name.0' => 'required|unique:chadhava,name'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_field_is_required'),
            'name.0.required' => translate('the_name_field_is_required'),
            'image.required' => translate('the_image_is_required'),
        ];
    }

}
