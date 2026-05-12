<?php

namespace App\Http\Requests\Vendor;

use App\Traits\ResponseHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VendorRequest extends FormRequest
{
    use ResponseHandler;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize():bool
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

        $rules = [
            'f_name' => 'required',
            'l_name' => 'required',
            'image' => 'mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff',
            'phone'  => 'required|unique:sellers,phone,' . $this->id,
            'gst' => 'required',
            'aadhar_number' => 'required',
            'pan_number' => 'required',
        ];
        $record = \App\Models\Seller::find($this->id);

        if ($record) {
            $rules['aadhar_front_image'] = $record->aadhar_front_image
                ? 'nullable|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff'
                : 'required|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff';

            $rules['aadhar_back_image'] = $record->aadhar_back_image
                ? 'nullable|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff'
                : 'required|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff';

            $rules['pancard_image'] = $record->pancard_image
                ? 'nullable|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff'
                : 'required|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff';
        } else {
            $rules['aadhar_front_image'] = 'required|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff';
            $rules['aadhar_back_image'] = 'required|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff';
            $rules['pancard_image'] = 'required|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff';
        }
        return $rules;
    }

    /**
     * @return array
     */
    public function messages():array
    {
        return [
            'f_name.required' => translate('first_name_is_required').'!',
            'l_name.required' =>translate('last_name_is_required').'!',
            'image.mimes' => translate('The_image_type_must_be').'.jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff,.webp',
            'phone.required' =>translate('phone_number_is_required').'!',
            'gst.required' => translate('gst_is_required').'!',
            'aadhar_number.required' => translate('aadhar_is_required').'!',
            'aadhar_front_image.required' => translate('aadhar_front_image_is_required').'!',
            'aadhar_back_image.required' => translate('aadhar_back_image_is_required').'!',
            'pan_number.required' => translate('pancard_is_required').'!',
            'pancard_image.required' => translate('pancard_image_is_required').'!',
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $this->errorProcessor($validator)]));
    }
}