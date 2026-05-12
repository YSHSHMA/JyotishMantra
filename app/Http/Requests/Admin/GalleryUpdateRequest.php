<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class GalleryUpdateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'images' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => translate('the_title_field_is_required'),
            'images.required' => translate('the_images_field_is_required')
        ];
    }

}