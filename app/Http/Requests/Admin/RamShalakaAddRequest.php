<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class RamShalakaAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => 'required|array'
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => translate('the_name_field_is_required'),
            'description.0.required' => translate('the_name_field_is_required')
        ];
    }

}
