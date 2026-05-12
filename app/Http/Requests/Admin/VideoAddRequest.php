<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @property int $id
 * @property string $name
 * @property string $image
 * @property int $status
 */
class VideoAddRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

 
public function rules(): array
{
    return [

        'title' => 'required',
        'url' => 'required',
    ];
}


    public function messages(): array
    {
        return [
            'title.required' => translate('the_name_field_is_required'),

        ];
    }

  
}

