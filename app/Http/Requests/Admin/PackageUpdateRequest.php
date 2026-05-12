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
class PackageUpdateRequest extends FormRequest
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
            'person' => 'required',
            'color' => 'required',
            'description' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => translate('the_title_field_is_required'),
            'person.required' => translate('the_person_field_is_required'),
            'color.required' => translate('the_color_field_is_required'),
            'description.required' => translate('the_description_field_is_required')
        ];
    }

}