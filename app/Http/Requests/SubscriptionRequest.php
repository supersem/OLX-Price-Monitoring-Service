<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ad_url' => [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?olx\.(com|ua)(\/.*)?$/i'
            ],
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'ad_url.required' => 'Please provide the URL of the OLX ad.',
            'ad_url.url' => 'The provided URL is not valid. Please enter a valid URL.',
            'ad_url.regex' => 'The URL must be a valid OLX ad link (e.g., https://www.olx.com/item/example).',
            'email.required' => 'Please provide an email address to receive notifications.',
            'email.email' => 'The provided email address is not valid. Please enter a valid email address.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
