<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class OfflinePoojaCategoryAddRequest extends FormRequest
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
            'image' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_field_is_required'),
            'image.required' => translate('the_image_field_is_required'),
        ];
    }

}
