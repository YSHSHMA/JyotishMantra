<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class OfflinePoojaRefundPolicyAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'days' => 'required',
            'percent' => 'required',
            'message' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_field_is_required'),
            'days.required' => translate('the_days_field_is_required'),
            'percent.required' => translate('percent_field_is_required'),
        ];
    }

}
