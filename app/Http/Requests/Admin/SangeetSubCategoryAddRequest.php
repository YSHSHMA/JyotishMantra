<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class SangeetSubCategoryAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

 
     public function rules()
    {
        $categoryId = $this->input('category_id'); // Get the selected category ID

        return [
            'category_id' => 'required|integer|exists:sangeet_categories,id', // Ensure category exists
            'name' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($categoryId) {
                    foreach ($value as $index => $name) {
                        $exists = \App\Models\SangeetSubCategory::where('category_id', $categoryId)
                            ->where('name', $name)
                            ->exists();

                        if ($exists) {
                            $fail(translate('the_name_field_is_duplicate_for_the_selected_category'));
                        }
                    }
                }
            ],
            'name.*' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => translate('category_id_is_required'),
            'category_id.exists' => translate('selected_category_does_not_exist'),
            'name.required' => translate('the_name_field_is_required'),
            'name.*.required' => translate('the_name_field_is_required!'),
        ];
    }
}
