<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


class Cities_visitsRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month_name' => 'required',
            'season' => 'required',
            'crowd' => 'required',
            'weather' => 'required',
            'sight' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'month_name.required' => translate('the_month_name_field_is_required'),
            'season.required' => translate('the_season_is_required'),
            'crowd.required' => translate('the_crowd_is_required'),
            'weather.required' => translate('the_weather_is_required'),
            'sight.required' => translate('the_sight_is_required'),
        ];
    }

}