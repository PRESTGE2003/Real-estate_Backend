<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'min:6', 'max:100',
                'confirmed',
                'regex:/[A-Za-z0-9]/',
                'regex:/^(?=.*[!@#$%^&*()\-_=+{};:,<.>]).*$/',
                'regex:/^(?=.*\d).*$/'
            ],
            'newsLetter' => ['required', 'boolean']
        ];
    }
}
