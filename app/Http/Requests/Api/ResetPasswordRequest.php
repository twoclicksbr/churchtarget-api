<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'token' => 'required|string|size:6',
            'password' => 'required|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'token.size' => 'O código deve conter exatamente 6 dígitos.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
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
