<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'user_name' => 'required|string|unique:users,user_name',
            'birthday' => 'nullable|date',
            'street_address' => 'nullable|string',
            'municipality' => 'nullable|string',
            'province' => 'nullable|string',
            'email' => ['required', 'email', 'unique:users,email','ends_with:bpsu.edu.ph'],
            'gender' => 'nullable|string',
            'phone_number' => 'nullable|digits:11',
            'password' => [
                'confirmed',
                'required',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->symbols()
                    ->numbers()
            ],
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'student_id' => 'required|regex:/[0-9]{2}-[0-9]{5}/',
            'program' => 'required|string',
        ];
    }
}
