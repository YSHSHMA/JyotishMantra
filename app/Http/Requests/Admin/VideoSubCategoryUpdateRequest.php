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
class VideoSubCategoryUpdateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'image' => 'image'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_is_required')
        ];
    }


}
