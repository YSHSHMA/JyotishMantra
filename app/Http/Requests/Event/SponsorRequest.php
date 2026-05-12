<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class SponsorRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'type' => 'required|in:sponsor,complimentary',
            'name' => 'required',
            'company_name' => 'required_if:type,sponsor',
            'person_phone' => 'required',
            'link' => 'required_if:type,sponsor',
            'package_id' => 'required',
            'image' => $id ? 'nullable|file|mimes:jpg,jpeg,png,webp' : 'required|file|mimes:jpg,jpeg,png,webp',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>  translate('the_name_field_is_required'),
            'profession.required' =>  translate('the_profession_field_is_required'),
        ];
    }
}
