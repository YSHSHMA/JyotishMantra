<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class OfflinePoojaRefundPolicyUpdateRequest extends FormRequest
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
            'days.required' => translate('the_days_field_is_required'),
            'percent.required' => translate('percent_field_is_required'),
            'message.required' => translate('the_message_field_is_required'),
        ];
    }

}
