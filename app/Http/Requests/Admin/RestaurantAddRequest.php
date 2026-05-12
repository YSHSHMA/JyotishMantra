<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'restaurant_name'=>'required|array',
            'restaurant_name.*' => 'required|string|min:1',
            'latitude' => 'required',
            'longitude' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'restaurant_name.required' => translate('rastaurant_name_is_required'),
            'latitude.required' => translate('rastaurant_name_is_required'),
            'longitude.required' => translate('rastaurant_name_is_required'),
        ];
    }
}
