<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;



/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class SangeetAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

 
// public function rules(): array
// {
//     return [

//         'category_id' => 'required',
//         'subcategory_id' => 'required',
//         'language' => 'required',
//     ];
// }


public function rules()
{
    return [
        'category_id' => [
            'required',
            function ($attribute, $value, $fail) {
                $subcategory_id = $this->input('subcategory_id');
                $language = $this->input('language');

                // Check if the combination already exists
                $exists = DB::table('sangeets')
                    ->where('category_id', $value)
                    ->where('subcategory_id', $subcategory_id)
                    ->where('language', $language)
                    ->exists();

                if ($exists) {
                    $fail('The combination of category, subcategory, and language already exists.');
                }
            },
        ],
        'subcategory_id' => 'required',
        'language' => 'required',
    ];
}

    public function messages(): array
    {
        return [
            'language.required' => translate('the_language_field_is_required'),

        ];
    }

  
}

