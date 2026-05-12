<?php

namespace App\Http\Requests\Tour;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $about_us
 */
class DriverRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'numeric',
                'between:1000000000,999999999999',
                Rule::unique('tour_traveller_driver', 'phone')->ignore($this->id),
            ],
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date|before:-18 years',
            'year_ex' => 'required|integer|min:0',
            'license_number' => [
                'required',
                Rule::unique('tour_traveller_driver', 'license_number')
                    ->ignore($this->id)
                    ->where(function ($query) {
                        $query->where('license_number', $this->license_number);
                    }),
            ],
            'pan_number' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9 ]{1,15}$/',
                Rule::unique('tour_traveller_driver', 'pan_number')->ignore($this->id),
            ],
            'aadhar_number' => [
                'required',
                'string',
                'regex:/^\d{12}$/',
                Rule::unique('tour_traveller_driver', 'aadhar_number')->ignore($this->id),
            ],
        ];
        if ($this->has('id')) {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
            $rules['license_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
            $rules['pan_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
            $rules['aadhar_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        } else {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
            $rules['license_image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
            $rules['pan_image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
            $rules['aadhar_image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            // Name
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name must not exceed 255 characters.',

            // Phone
            'phone.required' => 'The phone number is required.',
            'phone.digits' => 'The phone number must be exactly 10 digits.',
            'phone.unique' => 'This phone number is already registered.',

            // Email
            'email.email' => 'The email address must be a valid email.',
            'email.max' => 'The email address must not exceed 255 characters.',

            // Gender
            'gender.required' => 'The gender is required.',
            'gender.in' => 'The gender must be one of the following: male, female, or other.',

            // Date of Birth
            'dob.required' => 'The date of birth is required.',
            'dob.date' => 'The date of birth must be a valid date.',
            'dob.before' => 'The date of birth must be a valid date before 18 yaer+.',

            // Year of Experience
            'year_ex.required' => 'The years of experience are required.',
            'year_ex.integer' => 'The years of experience must be an integer.',
            'year_ex.min' => 'The years of experience must be at least 0.',

            // License Number
            'license_number.required' => 'The license number is required.',
            'license_number.string' => 'The license number must be a valid string.',
            'license_number.regex' => 'The license number can only contain letters, numbers, and spaces, and must not exceed 15 characters.',
            'license_number.unique' => 'This license number is already registered.',

            // PAN Number
            'pan_number.required' => 'The PAN number is required.',
            'pan_number.string' => 'The PAN number must be a valid string.',
            'pan_number.regex' => 'The PAN number can only contain letters, numbers, and spaces, and must not exceed 15 characters.',
            'pan_number.unique' => 'This PAN number is already registered.',

            // Aadhar Number
            'aadhar_number.required' => 'The Aadhar number is required.',
            'aadhar_number.string' => 'The Aadhar number must be a valid string.',
            'aadhar_number.regex' => 'The Aadhar number must be exactly 12 numeric digits.',
            'aadhar_number.unique' => 'This Aadhar number is already registered.',

            // Image
            'image.required' => 'The image is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be in jpeg, png, jpg, or gif format.',
            'image.max' => 'The image must not exceed 2 MB.',

            // license_image
            'license_image.required' => 'The license image is required.',
            'license_image.image' => 'The file must be an license image.',
            'license_image.mimes' => 'The license image must be in jpeg, png, jpg, or gif format.',
            'license_image.max' => 'The license image must not exceed 2 MB.',


            // pan_image
            'pan_image.required' => 'The pan image is required.',
            'pan_image.image' => 'The file must be an pan image.',
            'pan_image.mimes' => 'The pan image must be in jpeg, png, jpg, or gif format.',
            'pan_image.max' => 'The pan image must not exceed 2 MB.',


            // aadhar_image
            'aadhar_image.required' => 'The Aadhar image is required.',
            'aadhar_image.image' => 'The file must be an Aadhar image.',
            'aadhar_image.mimes' => 'The Aadhar image must be in jpeg, png, jpg, or gif format.',
            'aadhar_image.max' => 'The Aadhar image must not exceed 2 MB.',
        ];
    }
}
