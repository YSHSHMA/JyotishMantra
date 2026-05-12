<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class SangeetSubCategoryUpdateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

     public function rules(): array
    {
        $categoryId = $this->input('category_id'); // Get the category ID for validation
        $subCategoryId = $this->route('id'); // Assuming the subcategory ID is passed via route

        return [
            'category_id' => 'required|integer|exists:sangeet_categories,id', // Ensure category exists
            'name' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($categoryId, $subCategoryId) {
                    foreach ($value as $index => $name) {
                        // Check for existing names under the same category except for the current subcategory
                        $exists = \App\Models\SangeetSubCategory::where('category_id', $categoryId)
                            ->where('name', $name)
                            ->where('id', '!=', $subCategoryId)
                            ->exists();

                        if ($exists) {
                            $fail(translate('the_name_field_is_duplicate_for_the_selected_category'));
                        }
                    }
                },
            ],
            'name.*' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => translate('category_id_is_required'),
            'category_id.exists' => translate('selected_category_does_not_exist'),
            'name.required' => translate('the_name_field_is_required'),
            'name.*.required' => translate('the_name_field_is_required!'),
        ];
    }


}
