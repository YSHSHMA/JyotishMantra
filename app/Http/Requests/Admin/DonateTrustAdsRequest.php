<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DonateTrustAdsRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|array',
            'name.*' => 'required|string|min:1',
            'purpose_id' => 'required',
            'set_type' => 'required',
            'type' => 'required',
            'description' => 'required|array',
            'description.*' => 'required|string|min:1',
            'admin_commission'=>'nullable|numeric',
        ];

        if ($this->filled('type') && $this->input('type') == 'outsite') {
            $rules["category_id"] = 'required';
            $rules["trust_id"] = 'required';
        }

        if ($this->filled('set_type') && $this->input('set_type') == '1') {
            $rules["set_amount.en.*"] = 'required|numeric';
            $rules["set_number.en.*"] = 'nullable|numeric';
            $rules["set_unit.en.*"] = 'required|string';
        }

        $rules["image"] = $this->filled('id')
            ? "nullable|image|mimes:jpg,png,jpeg,gif,bmp,tif,tiff"
            : "required|image|mimes:jpg,png,jpeg,gif,bmp,tif,tiff";

        return $rules;
    }


    public function messages(): array
    {
        return [
            'name.required' =>  translate('the_name_field_is_required'),
            'category_id.required' =>  translate('the_category_field_is_required'),
            'trust_id.required' =>  translate('the_trust_name_field_is_required'),
            'purpose_id.required' =>  translate('the_purpose_field_is_required'),
            'type.required' =>  translate('Please_select_type_field_is_required'),
            'set_type.required' =>  translate('the_set_type_field_is_required'),
            'description.required' =>  translate('the_description_field_is_required'),
            'set_amount.required' =>  translate('the_amount_field_is_required'),
            'set_unit.required' =>  translate('the_set_unit_field_is_required'),
            'image.required' => translate('the_image_field_is_required'),
        ];
    }
}
