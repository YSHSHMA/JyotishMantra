<?php

namespace App\Http\Requests;

use App\Contracts\Repositories\ServiceRepositoryInterface;
use App\Traits\CalculatorTrait;
use App\Traits\ResponseHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ServiceUpdateRequest extends FormRequest
{
    use CalculatorTrait, ResponseHandler;
    protected $stopOnFirstFailure = true;

    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepo
    )
    {}

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $service = $this->serviceRepo->getFirstWhere(['id' => $this->route('id')]);
        return [
            // 'name' => 'required|array',
            'image' => 'image',
        ];
    }

    public function messages(): array
    {
        return [
            // 'name.required' => translate('the_name_field_is_required'),
        ];
    }

}
