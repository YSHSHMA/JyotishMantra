<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AstrologerGiftUpdateRequest extends FormRequest {

    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'amount' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('name_is_required'),
            'amount.required' => translate('amount_is_required')
        ];
    }
}