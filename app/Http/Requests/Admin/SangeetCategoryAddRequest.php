<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class SangeetCategoryAddRequest extends FormRequest
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
            'image' => 'required|image',
            'name.0' => 'required|unique:sangeet_categories,name'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => translate('the_name_field_is_required'),
            'name.*.required' => translate('the_name_field_is_required!'),
            'name.0.unique' => translate('The_category_has_already_been_taken'),
            'image.required' => translate('the_image_field_is_required'),
            'image.image' => translate('the_image_must_be_an_image'),
        ];
    }
}
