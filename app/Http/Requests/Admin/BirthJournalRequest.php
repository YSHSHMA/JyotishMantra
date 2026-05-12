<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $id
 * @property string $url
 * @property int $status
 */
class BirthJournalRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'selling_price' => 'required|numeric',
            'pages' => 'required|integer',
            'name' => 'required',
            'type' => 'required',
            'short_description'=>"required|array",
            'short_description.*'=>"required|string|min:1",
            'description' => 'required|array',
            'description.*' => 'required|string|min:1',
        ];

        if ($this->has('id')) {
            $rules['image'] = 'nullable|image|mimes:jpg,png,jpeg,gif,bmp,tif,tiff';
        } else {
            $rules['image'] = 'required|image|mimes:jpg,png,jpeg,gif,bmp,tif,tiff';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'selling_price.required' => translate('the_selling_price_field_is_required'),
            'pages.required' => translate('the_pages_field_is_required'),
            'name.required' => translate('the_name_field_is_required'),
            'type.required' => translate('the_type_field_is_required'),
            'short_description.required' => translate('the_short_description_field_is_required'),
            'description.required' => translate('the_description_field_is_required'),
            'image.required' => translate('the_image_field_is_required'),
        ];
    }

}
