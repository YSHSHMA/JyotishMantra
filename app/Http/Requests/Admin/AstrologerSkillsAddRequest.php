<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AstrologerSkillsAddRequest extends FormRequest {

    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('skills_is_request'),
        ];
    }
}