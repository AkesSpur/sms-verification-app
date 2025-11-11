<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Sanitize input before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower((string) $this->input('email')),
            // keep only digits in phone input
            'phone' => preg_replace('/\D+/', '', (string) $this->input('phone')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // digits only, maximum 11 digits
            'phone' => [
                'required',
                'string',
                'regex:/^\d{1,11}$/',
                Rule::unique('users', 'phone')->ignore($this->user()->id),
            ],
        ];
    }
}
