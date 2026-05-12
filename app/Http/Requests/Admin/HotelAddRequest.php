<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HotelAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_name' => 'required|array',
            'hotel_name.*' => 'required|string|min:1',
            'country_id' => 'required',
            'state_id' => 'required',
            'cities_id' => 'required',
            'zipcode' => 'required',
            'phone_no' => 'required',
            'email_id' => 'required',
            'website_link' => 'required',
            'description' => 'required',
            'amenities' => 'required',
            'room_amenities' => 'required',
            'room_types' => 'required',
            'booking_information' => 'required',
            ];
    }

    public function messages(): array
    {
        return [
            'hotel_name.required' =>  translate('the_hotel_name_field_is_required') ,
            'country_id.required' =>  translate('the_country_id_field_is_required') ,
            'state_id.required' =>  translate('the_state_id_field_is_required') ,
            'cities_id.required' =>  translate('the_cities_id_field_is_required') ,
            'zipcode.required' =>  translate('the_zipcode_field_is_required') ,
            'phone_no.required' =>  translate('the_phone_no_field_is_required') ,
            'email_id.required' =>  translate('the_email_id_field_is_required') ,
            'website_link.required' =>  translate('the_website_link_field_is_required') ,
            'description.required' =>  translate('the_description_field_is_required') ,
            'amenities.required' =>  translate('the_amenities_field_is_required') ,
            'room_amenities.required' =>  translate('the_room_amenities_field_is_required') ,
            'room_types.required' =>  translate('the_room_types_field_is_required') ,
            'booking_information.required' => translate('the_booking_information_field_is_required') ,
        ];
    }

}
