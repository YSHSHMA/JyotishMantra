<?php

namespace App\Http\Requests\Admin;

use App\Contracts\Repositories\FaqRepositoryInterface;
use App\Traits\CalculatorTrait;
use App\Traits\ResponseHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FAQUpdateRequest extends FormRequest
{
    use CalculatorTrait, ResponseHandler;
    protected $stopOnFirstFailure = true;

    public function __construct(
        private readonly FaqRepositoryInterface $FaqRepo
    )
    {}

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $faq = $this->FaqRepo->getFirstWhere(['id' => $this->route('id')]);
        return [
            'question' => 'required|array',
            'question.*' => 'string|min:5',
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => translate('the_question_field_is_required'),
        ];
    }

}