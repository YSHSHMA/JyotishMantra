<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TourAndTravelRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'owner_name' => 'required',
            'company_name' => 'required',
            'phone_no' => 'required',
            'email' => 'required|email',
            // 'state' => 'required|state',
            // 'city' => 'required|city',
            'address' => 'required',
            'web_site_link' => 'required',
            'services' => 'required',
            'area_of_operation' => 'required',
            'person_name' => 'required',
            'person_phone' => 'required',
            'person_email' => 'required|email',
            'person_address' => 'required',

            'bank_holder_name' => 'required',
            'bank_name' => 'required',
            'bank_branch' => 'required',
            'ifsc_code' => 'required',
            'account_number' => 'required',
        ];

            if (!$this->has('id')) {
            $rules['gst_image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            $rules['pan_card_image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            $rules['aadhaar_card_image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            $rules['address_proof_image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            $rules['banner'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            }
            return $rules;
    }

    public function messages(): array
    {
        return [
            'owner_name.required' => translate('the_owner_name_field_is_required!'),
            'company_name.required' => translate('the_company_name_field_is_required!'),
            'phone_no.required' => translate('the_phone_number_field_is_required!'),
            'email.required' => translate('the_email_field_is_required!'),
            // 'state.required' => translate('the_state_field_is_required!'),
            // 'city.required' => translate('the_city_field_is_required!'),
            'address.required' => translate('the_address_field_is_required!'),
            'web_site_link.required' => translate('the_web_site_link_field_is_required!'),
            'services.required' => translate('the_services_field_is_required!'),
            'area_of_operation.required' => translate('the_area_of_operation_field_is_required!'),
            'person_name.required' => translate('the_person_name_field_is_required!'),
            'person_phone.required' => translate('the_person_phone_field_is_required!'),
            'person_email.required' => translate('the_person_email_field_is_required!'),
            'person_address.required' => translate('the_person_address_field_is_required!'),
            'bank_holder_name.required' => translate('the_bank_holder_name_field_is_required!'),
            'bank_name.required' => translate('the_bank_name_field_is_required!'),
            'bank_branch.required' => translate('the_bank_branch_field_is_required!'),
            'ifsc_code.required' => translate('the_ifsc_code_field_is_required!'),
            'account_number.required' => translate('the_account_number_field_is_required!'),

            'gst_image.required' => translate('the_gst_image_is_required!'),
            'pan_card_image.required' => translate('the_pan_card_image_is_required!'),
            'aadhaar_card_image.required' => translate('the_aadhaar_card_image_is_required!'),
            'address_proof_image.required' => translate('the_address_proof_image_is_required!'),
            'banner.required' => translate('the_banner_image_is_required!'),
        ];
    }
}
