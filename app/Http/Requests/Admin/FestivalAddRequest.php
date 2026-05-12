<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class FestivalAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => 'required',
            'date' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png',
            'title' => 'required',
            'tithi' => 'required',
            'detail' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'month.required' => translate('the_month_field_is_required'),
            'date.required' => translate('the_date_field_is_required'),
            'title.required' => translate('the_title_field_is_required'),
            'tithi.required' => translate('the_month_tithi_is_required'),
            'detail.required' => translate('the_month_detail_is_required'),
           
            'image.required' => translate('the_image_is_required'),
        ];
    }

}
