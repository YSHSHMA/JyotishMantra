<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class MasikRashiAddRequest extends FormRequest
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
            'month' => 'required',
            'lang' => 'required',
            'akshar' => 'required',
            'detail' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_rashi_name_field_is_required'),
            'month.required' => translate('the_month_name_field_is_required'),
            'lang.unique' => translate('the_lang_name_field_is_required'),
            'akshar.required' => translate('the_image_is_required'),
        ];
    }

}
