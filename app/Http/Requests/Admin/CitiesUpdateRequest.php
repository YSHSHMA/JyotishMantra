<?php

namespace App\Http\Requests\Admin;

use App\Contracts\Repositories\CitiesRepositoryInterface;
use App\Traits\CalculatorTrait;
use App\Traits\ResponseHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CitiesUpdateRequest extends FormRequest
{
    use CalculatorTrait, ResponseHandler;
    protected $stopOnFirstFailure = true;

    public function __construct(
        private readonly CitiesRepositoryInterface $citiesRepo
    )
    {}

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cities = $this->citiesRepo->getFirstWhere(['id' => $this->route('id')]);
        return [
            'short_desc' => 'required|array',
            'longitude'=>'required',
            'latitude'=>'required',
        ];
    }

    public function messages(): array
    {
        return [
            'short_desc.required' => translate('the_name_field_is_required'),            
            'longitude.required' => translate('the_city_is_required'),
            'latitude.required' => translate('the_city_is_required'),
        ];
    }

}