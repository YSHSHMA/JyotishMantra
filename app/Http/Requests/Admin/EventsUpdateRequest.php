<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventsUpdateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_name' => 'required|array',
            'event_name.*' => 'required|string|min:1',
            'category_id' => 'required',
            'organizer_by' => 'required',
            'event_organizer_id' => 'required',
            'event_about' => 'required|array',
            'event_about.*' => 'required|string|min:1',
            'event_schedule' => 'required|array',
            'event_schedule.*' => 'required|string|min:1',
            'event_attend' => 'required|array',
            'event_attend.*' => 'required|string|min:1',
            'event_team_condition' => 'required|array',
            'event_team_condition.*' => 'required|string|min:1',
            'age_group' => 'required',
            'event_artist' => 'required',
            'language' => 'required',
            'days' =>  ['required', 'integer', 'min:1'],
            'start_to_end_date' => ['required', function ($attribute, $value, $fail) {
                if ($this->days == 1) {
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                        $fail('The ' . $attribute . ' must be a single date (YYYY-MM-DD) when days is 1.');
                    }
                } else {
                    if (!preg_match('/^\d{4}-\d{2}-\d{2} - \d{4}-\d{2}-\d{2}$/', $value)) {
                        $fail('The ' . $attribute . ' must be a date range (YYYY-MM-DD - YYYY-MM-DD) when days is greater than 1.');
                    }
                }
            }],

            // 'event_venue' => 'required|array',
            // 'event_venue.*' => 'required|string|min:1',
            // 'date' => 'required|array',
            // 'date.*' => 'required|string|min:1',
            // 'start_time' => 'required|array',
            // 'start_time.*' => 'required|string|min:1',
            // 'end_time' => 'required|array',
            // 'end_time.*' => 'required|string|min:1',
            // 'event_duration' => 'required|array',
            // 'event_duration.*' => 'required|string|min:1',
            'venue' => 'required|array',
            'venue.*.en_event_venue' => 'required|string|max:255',
            'venue.*.hi_event_venue' => 'required|string|max:255',
            'venue.*.date' => 'required|date',
            'venue.*.date' => 'required|date',
            'venue.*.start_time' => 'required|date_format:h:i A',
            'venue.*.end_time' => 'required|date_format:h:i A',
        ];

        if ($this->filled('informational_status') == 0) {
            $rules["venue.*.package_list"] = 'required';
            // $rules["packeage_id"] = 'required|array';
            // $rules['packeage_id.*'] = 'required|string|min:1';
            // $rules['seats_no'] = 'required|array';
            // $rules['seats_no.*'] = 'required|string|min:1';
            // $rules['price'] = 'required|array';
            // $rules['price.*'] = 'required|string|min:1';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'event_name.required' =>  translate('the_name_field_is_required'),
            'category_id.required' =>  translate('the_category_field_is_required'),
        ];
    }
}
