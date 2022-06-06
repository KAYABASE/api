<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100', 'unique:tenants'],
            'phone' => ['nullable', 'string', 'max:12'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::min(6)],
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Adınızı girmelisiniz.',
            'last_name.required' => 'Soyadınızı girmelisiniz.',
            'name.required' => 'Firma adınızı girmelisiniz.',
            'email.required' => 'E-Posta adresinizi girmelisiniz.',
            'email.unique' => 'Bu E-Posta adresine ait bir üyelik bulunmaktadır.',
            'password.required' => 'Parola girmelisiniz.',
            'name.unique' => 'Bu Firma adıyla mevcut bir kayıt var.',
        ];
    }

    public function payload()
    {
        return $this->only(array_keys($this->rules()));
    }
}
