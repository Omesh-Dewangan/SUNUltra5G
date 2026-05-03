<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Prepare the data for validation.
     * Convert empty password strings to null so 'nullable' rules work correctly.
     */
    protected function prepareForValidation()
    {
        // If password is empty string, set to null to skip password validation
        if ($this->password === '' || $this->password === null) {
            $this->merge([
                'password'              => null,
                'password_confirmation' => null,
                'current_password'      => null,
            ]);
        }
    }

    public function rules()
    {
        $userId = Auth::id();
        return [
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users,email,' . $userId,
            'current_password' => 'nullable|required_with:password|string',
            'password'         => 'nullable|required_with:current_password|string|min:8|confirmed',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Only run current_password check if user wants to change password
            if ($this->filled('password')) {
                if (!$this->filled('current_password')) {
                    $validator->errors()->add('current_password', 'Current password is required to set a new password.');
                } elseif (!Hash::check($this->current_password, Auth::user()->password)) {
                    $validator->errors()->add('current_password', 'The current password you entered is incorrect.');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'password.confirmed'     => 'New password and confirm password do not match.',
            'password.min'           => 'New password must be at least 8 characters.',
            'password.required_with' => 'Please enter a new password to proceed.',
            'current_password.required_with' => 'Current password is required to change your password.',
            'name.required'          => 'Full name is required.',
            'email.unique'           => 'This email address is already taken.',
        ];
    }
}
