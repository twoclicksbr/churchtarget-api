<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyRecoveryCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'token' => 'required|string|size:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'token.required' => 'O código é obrigatório.',
            'token.size' => 'O código deve conter exatamente 6 dígitos.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error' => 'Erro de validação.',
            'messages' => $validator->errors()
        ], 422));
    }
}
