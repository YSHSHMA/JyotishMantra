<?php

namespace App\Http\Requests\Admin;

use App\Traits\ResponseHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

/**
 * Class VideoCategory
 *
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class videocategoryRequest extends FormRequest
{
    use ResponseHandler;

    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.*' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('the_name_field_is_required!'),
            'name.*.required' => translate('the_name_field_is_required!'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (is_null($this->input('name.' . array_search('en', $this->input('lang'))))) {
                $validator->errors()->add(
                    'name', translate('name_field_is_required') . '!'
                );
            }
        });
    }
}
