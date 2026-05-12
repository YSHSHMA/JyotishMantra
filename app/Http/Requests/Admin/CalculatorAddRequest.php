<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class CalculatorAddRequest extends FormRequest
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
            'description' => 'required',
            'logo' => 'required|image|mimes:jpg,jpeg,png',
            'detail_image' => 'required|image|mimes:jpg,jpeg,png'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_field_is_required'),
            'detail.required' => translate('the_detail_field_is_required'),
            'image.required' => translate('the_image_is_required'),
            'detail_image.required' => translate('the_detail_image_is_required'),
        ];
    }

}
