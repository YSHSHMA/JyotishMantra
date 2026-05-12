<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventOrganizeUpdateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organizer_name' => 'required|array',
            'organizer_name.*' =>'required|string|min:1',
            'organizer_pan_no' =>'required',
            'organizer_address' =>'required|array',
            'organizer_address.*' =>'required|string|min:1',
            'gst_no_type' =>'required',
            'itr_return' =>'required',
            'full_name' =>'required',
            'email_address' =>'required',
            'contact_number' =>'required',
            'beneficiary_name' =>'required',
            'account_type' =>'required',
            'bank_name' =>'required',
            'ifsc_code' =>'required',
            'account_no' =>'required',
            ];
    }

    public function messages(): array
    {
        return [
            'organizer_name.required' =>  translate('the_organizer_name_field_is_required'),
        ];
    }

}
