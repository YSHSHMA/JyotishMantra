<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


class FAQAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => 'required',
            'detail' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => translate('the_title_field_is_required'),
            'detail.required' => translate('the_month_detail_is_required'),
        ];
    }

}
