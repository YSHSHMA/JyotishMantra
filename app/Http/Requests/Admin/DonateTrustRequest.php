<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DonateTrustRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'category_id' => "required",
            'name' => 'required|array',
            'name.*' => 'required|string|min:1',
            'trust_name' => 'required|array',
            'trust_name.*' => 'required|string|min:1',
            'description' => 'required|array',
            'description.*' => 'required|string|min:1',
            'trust_pan_card' => 'required',
            'pan_card' => 'required',
            'full_address' => 'required|array',
            'full_address.*' => 'required|string|min:1',
            'member_name' => 'required|array',
            'member_name.*' => 'required|string|min:1',
            'member_phone_no' => 'required|array',
            'member_phone_no.*' => 'required|min:13',
            'member_position' => 'required|array',
            'member_position.*' => 'required|string|min:1',
            'beneficiary_name' => 'required|string',
            'account_type' => 'required',
            'bank_name' => 'required',
            'ifsc_code' => 'required',
            'account_no' => 'required',
            'c_account_no' => 'required|same:account_no',
            "csr_certificate" => "nullable|mimes:jpg,png,jpeg,webp,pdf,doc,docx",
            "frc_certificate" => "nullable|mimes:jpg,png,jpeg,webp,pdf,doc,docx",
        ];
        if ($this->filled('id')) {
            $rules["trust_email"] = ['required', 'email', Rule::unique('sellers', 'email')->ignore($this->input('id'), 'relation_id')];
        } else {
            $rules["trust_email"] = ['required', 'email', Rule::unique('sellers', 'email')];
        }

        $firstPhone = $this->input('member_phone_no.0');
        if (!empty($firstPhone)) {
            if ($this->filled('id')) {
                $rules['member_phone_no.0'] = [
                    'required',
                    'min:10',
                    'max:15',
                    Rule::unique('sellers', 'phone')->ignore($this->input('id'), 'relation_id')
                ];
            } else {
                $rules['member_phone_no.0'] = [
                    'required',
                    'min:10',
                    'max:15',
                    Rule::unique('sellers', 'phone')
                ];
            }
        }

        if ($this->filled('id')) {
            $rules["twelve_a_certificate"] = "nullable|mimes:jpg,png,jpeg,webp,pdf,doc,docx";
            $rules["eighty_g_certificate"] = "nullable|mimes:jpg,png,jpeg,webp,pdf,doc,docx";
            $rules["niti_aayog_certificate"] = "nullable|mimes:jpg,png,jpeg,webp,pdf,doc,docx";
            $rules["e_anudhan_certificate"] = "nullable|mimes:jpg,png,jpeg,webp,pdf,doc,docx";
            $rules['gallery_image'] = 'nullable|array';
            $rules['gallery_image.*'] = 'image|mimes:jpg,png,jpeg';
        } else {
            $rules["twelve_a_certificate"] = "required|mimes:jpg,png,jpeg,webp,pdf,doc,docx";
            $rules["eighty_g_certificate"] = "required|mimes:jpg,png,jpeg,webp,pdf,doc,docx";
            $rules["niti_aayog_certificate"] = "required|mimes:jpg,png,jpeg,webp,pdf,doc,docx";
            $rules["e_anudhan_certificate"] = "required|mimes:jpg,png,jpeg,webp,pdf,doc,docx";
            $rules['gallery_image'] = 'required|array';
            $rules['gallery_image.*'] = 'image|mimes:jpg,png,jpeg';
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'category_id.required' =>  translate('the_category_field_is_required'),
            'name.required' =>  translate('the_name_field_is_required'),
            'trust_name.required' =>  translate('the_trust_name_field_is_required'),
            'description.required' =>  translate('the_description_field_is_required'),
            'trust_pan_card.required' =>  translate('the_trust_pan_card_field_is_required'),
            'pan_card.required' =>  translate('the_pan_card_field_is_required'),
            'full_address.required' =>  translate('the_full_address_field_is_required'),
            'website.required' =>  translate('the_website_Link_field_is_required'),
            'member_name.required' =>  translate('the_member_name_field_is_required'),
            'member_phone_no.required' =>  translate('the_member_phone_number_field_is_required'),
            'member_position.required' =>  translate('the_member_position_field_is_required'),
            'beneficiary_name.required' =>  translate('the_beneficiary_name_field_is_required'),
            'account_type.required' =>  translate('the_account_type_field_is_required'),
            'bank_name.required' =>  translate('the_bank_name_field_is_required'),
            'ifsc_code.required' =>  translate('the_ifsc_code_field_is_required'),
            'account_no.required' =>  translate('the_account_no_field_is_required'),
            'c_account_no.required' =>  translate('the_c_account_no_field_is_required'),
            'twelve_a_certificate.required' => translate('the_12a_Certificate_image_field_is_required'),
            'eighty_g_certificate.required' => translate('the_80g_Certificate_image_field_is_required'),
            'niti_aayog_certificate.required' => translate('the_NITI_aayog_certificate_image_field_is_required'),
            'csr_certificate.required' => translate('the_csr_certificate_image_field_is_required'),
            'e_anudhan_certificate.required' => translate('the_E_anudhan_certificate_image_field_is_required'),
            'frc_certificate.required' => translate('the_FRC_certificate_image_field_is_required'),
            // 'gallery_image.required' => translate('the_gallery_image_field_is_required'),
        ];
    }
}
