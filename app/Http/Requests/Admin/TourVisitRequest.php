<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TourVisitRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'tour_name' => 'required|array',
            // 'created_id' => 'required',
            'tour_name.*' => 'required|string|min:1',
            'cities_name' => 'required|array',
            'cities_name.*' => 'required|string|min:1',
            'country_name' => 'required|array',
            'country_name.*' => 'required|string|min:1',
            'state_name' => 'required|array',
            'state_name.*' => 'required|string|min:1',

            'description' => 'required|array',
            'description.*' => 'required|string|min:1',
            'inclusion' => 'required|array',
            'inclusion.*' => 'required|string|min:1',
            'exclusion' => 'required|array',
            'exclusion.*' => 'required|string|min:1',
            'terms_and_conditions' => 'required|array',
            'terms_and_conditions.*' => 'required|string|min:1',
            'cancellation_policy' => 'required|array',
            'cancellation_policy.*' => 'required|string|min:1',
        ];

        if (!$this->has('is_person_use')) {
            $rules['cab_id'] = 'required|array';
            $rules['cab_id.*'] = 'required|string|min:1';
            $rules['price'] = 'required|array';
            $rules['price.*'] = 'required|string|min:1';
        } else {
            $rules['min_person'] = 'required|array';
            $rules['min_person.*'] = 'required|numeric|min:1';
            $rules['max_person'] = 'required|array';
            $rules['max_person.*'] = 'required|numeric|min:1';
            $rules['person_price'] = 'required|array';
            $rules['person_price.*'] = 'required|numeric|min:1';
        }

        if (!$this->has('id')) {
            $rules['images'] = 'required';
            $rules['images.*'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'tour_name.required' => translate('the_tour_name_field_is_required!'),
            'tour_name.*.required' => translate('the_tour_name_field_is_required!'),
            'description.required' => translate('the_description_field_is_required!'),
            'description.*.required' => translate('the_description_field_is_required!'),
            'inclusion.required' => translate('the_inclusion_field_is_required!'),
            'inclusion.*.required' => translate('the_inclusion_field_is_required!'),
            'exclusion.required' => translate('the_exclusion_field_is_required!'),
            'exclusion.*.required' => translate('the_exclusion_field_is_required!'),
            'terms_and_conditions.required' => translate('the_terms_and_conditions_field_is_required!'),
            'terms_and_conditions.*.required' => translate('the_terms_and_conditions_field_is_required!'),
            'cancellation_policy.required' => translate('the_cancellation_policy_field_is_required!'),
            'cancellation_policy.*.required' => translate('the_cancellation_policy_field_is_required!'),

            'cab_id.required' => translate('the_cab_name_field_is_required!'),
            'cab_id.*.required' => translate('the_cab_name_field_is_required!'),

            'package_id.required' => translate('the_package_name_field_is_required!'),
            'package_id.*.required' => translate('the_package_name_field_is_required!'),

            // 'people.required' => translate('the_people_field_is_required!'),
            // 'people.*.required' => translate('the_people_field_is_required!'),

            'price.required' => translate('the_price_field_is_required!'),
            'price.*.required' => translate('the_price_field_is_required!'),


            'images.required' => translate('the_image_is_required!'),
        ];
    }
}
