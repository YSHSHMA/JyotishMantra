<?php

namespace App\Http\Requests\Tour;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $about_us
 */
class CabRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'cab_id' => 'required|integer|exists:tour_cab,id',
            'reg_number' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Za-z0-9 ]+$/',
                $this->has('id')
                    ? Rule::unique('tour_traveller_cabs', 'reg_number')->ignore($this->id)
                    : Rule::unique('tour_traveller_cabs', 'reg_number'),
            ],
            'model_number' => 'required|string|max:50',
        ];
        // if ($this->has('id')) {
        //     $rules['image'] = 'nullable|array';
        //     $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        // } else {
        //     $rules['image'] = 'required|array';
        //     $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        // }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'cab_id.required' => 'The cab ID is required.',
            'cab_id.exists' => 'The selected cab ID does not exist.',
            'reg_number.regex' => 'The registration number must only contain uppercase letters, numbers, and dashes.',
            'reg_number.unique' => 'The registration number already exists.',

            // Image
            'image.required' => 'The image is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be in jpeg, png, jpg, or gif format.',
            'image.max' => 'The image must not exceed 2 MB.',

        ];
    }
}
