<?php

namespace App\Traits;

trait TourVisitValidation
{
    protected function getTourVisitRules($step, $request)
    {
        return match ((int)$step) {
            1 => $this->stepOneRules(),
            2 => $this->stepTwoRules($request),
            3 => $this->stepThreeRules($request),
            default => $this->allStepsRules($request)
        };
    }

    protected function stepOneRules(): array
    {
        return [
            'tour_name' => 'required|array',
            'tour_name.*' => 'required|string|min:1',
            'created_id' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value != 0 && !\DB::table('tour_and_travels')->where('id', $value)->exists()) {
                        $fail('The selected ' . $attribute . ' is invalid.');
                    }
                },
            ],
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
    }

    protected function stepTwoRules($request): array
    {
        if (($request->input('is_person_use')??0) == 1) {
            return [
                'min_person' => 'required|array',
                'min_person.*' => 'required|numeric|min:1',
                'max_person' => 'required|array',
                'max_person.*' => 'required|numeric|min:1',
                'person_price' => 'required|array',
                'person_price.*' => 'required|numeric|min:0',
            ];
        }
        return [
            'cab_id' => 'required|array',
            'cab_id.*' => 'required|string|min:1',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',
        ];
    }

    protected function stepThreeRules($request): array
    {
        if (!$request->has('id')) {
            return [
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ];
        }
        return [];
    }

    protected function allStepsRules($request): array
    {
        $rules = [
            'tour_name' => 'required|array',
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

        if (($this->input('is_person_use') ?? 0) == 1) {
            $rules['min_person'] = 'required|array';
            $rules['min_person.*'] = 'required|numeric|min:1';
            $rules['max_person'] = 'required|array';
            $rules['max_person.*'] = 'required|numeric|min:1';
            $rules['person_price'] = 'required|array';
            $rules['person_price.*'] = 'required|numeric|min:1';
        } else {
            $rules['cab_id'] = 'required|array';
            $rules['cab_id.*'] = 'required|string|min:1';
            $rules['price'] = 'required|array';
            $rules['price.*'] = 'required|string|min:1';
        }

        if (!$this->has('id')) {
            $rules['images'] = 'required|array';
            $rules['images.*'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
        }
        return $rules;
    }
}
