<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AstrologerCategoryAddRequest extends FormRequest {

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
            'name.required' => translate('name_is_required'),
            'image.required' => translate('image_is_required'),
        ];
    }
}