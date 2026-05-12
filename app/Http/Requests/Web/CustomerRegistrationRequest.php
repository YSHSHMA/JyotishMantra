<?php

namespace App\Http\Requests\Web;

use App\Traits\CalculatorTrait;
use App\Traits\RecaptchaTrait;
use App\Traits\ResponseHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Validator;

class CustomerRegistrationRequest extends FormRequest
{
    use RecaptchaTrait;
    use CalculatorTrait, ResponseHandler;

    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'f_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|same:con_password',

        ];
    }

    public function messages(): array
    {
        return [
            'f_name.required' => translate('first_name_is_required'),
            'email.unique' => translate('email_already_has_been_taken'),
            'phone.required' => translate('phone_number_is_required'),
            'phone.unique' => translate('phone_number_already_has_been_taken'),
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $recaptcha = getWebConfig(name: 'recaptcha');
                if (isset($recaptcha) && $recaptcha['status'] == 1) {
                    if (!$this['g-recaptcha-response'] || !$this->isGoogleRecaptchaValid($this['g-recaptcha-response'])) {
                        $validator->errors()->add(
                            'recaptcha', translate('ReCAPTCHA_Failed') . '!'
                        );
                    }
                } else if ($recaptcha['status'] != 1 && strtolower($this['default_recaptcha_value_customer_regi']) != strtolower(Session('default_recaptcha_id_customer_regi'))) {
                    $validator->errors()->add(
                        'g-recaptcha-response', translate('ReCAPTCHA_Failed') . '!'
                    );
                } else if ($recaptcha['status'] != 1 && strtolower($this['default_recaptcha_value_customer_regi']) == strtolower(Session('default_recaptcha_id_customer_regi'))) {
                    Session::forget('default_recaptcha_id_customer_regi');
                }
            }
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $this->errorProcessor($validator)]));
    }
}
